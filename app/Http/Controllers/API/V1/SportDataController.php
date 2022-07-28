<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Library\GuzzleTrait;

use JWTAuth;
use App\Models\User;
use App\Models\BetList;
use App\Models\BetRequest;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Kutia\Larafirebase\Facades\Larafirebase;
use App\Models\Games;
use App\Models\Teams;
use App\Models\Transcation;
use Illuminate\Support\Facades\Http;
use App\Models\UserWallets;
use App\Models\RandomBet;
use Config;

use function PHPUnit\Framework\countOf;

class SportDataController extends Controller
{
    // Check Data Available or not on current Date 
    use GuzzleTrait;
    public function getGameByDate($league){
        $date = Carbon::now();
        $recordExist = Games::where('created_at','=',$date->toDateString())->where('League','=',$league)->get();    
        //echo count($recordExist);  
                if (count($recordExist)>0 && !empty($recordExist)) {
                    return response()->json([
                        'success' => true,
                        'data' => $recordExist,
                    ], 200);
            } else{
                if ($league == 'mlb' || $league == 'nba') {
                    $date = Carbon::now();
                    if ($league == 'mlb') {
                        $key = env('MLB_Key')!=null?env('MLB_Key'):Config::get('app.MLB_Key');
                    } else if ($league == 'nba') {
                        $key = env('NBA_Key')!=null?env('NBA_Key'):Config::get('app.NBA_Key');
                    }
                    $result = $this->guzzleRequest($league . '/scores/json/GamesByDate/' . $date->toDateString() . '?key=' . $key);
                    
                    $data = array();
                    if (!empty(json_decode($result->getBody()))) {
                        foreach (json_decode($result->getBody()) as $key => $value) {
                            $awayteam = Teams::where('team_id',$value->AwayTeamID)->where('league',$league)->select('city','name')->first();
                            $hometeam = Teams::where('team_id',$value->HomeTeamID)->where('league',$league)->select('city','name')->first();
                            
                            $data[] = ([
                                'GameID' => $value->GameID,
                                'League' => $league,
                                'Status' => $value->Status,
                                'Season' => $value->Season,
                                'Awayteam' => $value->AwayTeam,
                                'Hometeam' => $value->HomeTeam,
                                'AwayTeamID'=>$value->AwayTeamID,
                                'HomeTeamID' =>$value->HomeTeamID,
                                'AwayTeamName'=>$awayteam->city.' '.$awayteam->name,
                                'HomeTeamName'=>$hometeam->city.' '.$hometeam->name,
                                'DatetimeUTC' => $value->DateTimeUTC,
                                'created_at' => $date->toDateString(),
                            ]);
                        }
                        try {
                            Games::insert($data);
                        } catch (\Exception $e) {
                            //return report($e);
                            return response()->json([
                                'success' => false,
                                'message' =>  report($e),
                            ], 400);
                        }
        
                        $affectedResult = Games::where('created_at','=',$date->toDateString())->where('League','=',$league)->get();
                        if ($affectedResult) {
                            return response()->json([
                                'success' => true,
                                'data' => $affectedResult,
                            ], 200);
                        } else {
                            return response()->json([
                                'success' => false,
                                'data' => null,
                            ], 400);
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'data' => null,
                        ], 400);
                    }
                } else if ($league == 'nfl') {
                    $date = Carbon::now();
                    $key = env('NFL_Key')!=null?env('NFL_Key'):Config::get('app.NFL_Key');
                    $result = $this->guzzleRequest($league . '/scores/json/ScoresByDate/' . $date->toDateString() . '?key=' . $key);
                    $data = array();
                   
                    if (!empty(json_decode($result->getBody()))) {
                        foreach (json_decode($result->getBody()) as $key => $value) {
                            $awayteam = Teams::where('team_id',$value->AwayTeamID)->where('league',$league)->select('city','name')->first();
                            $hometeam = Teams::where('team_id',$value->HomeTeamID)->where('league',$league)->select('city','name')->first();
                            $data[] = ([
                                'GameID' => $value->GameID,
                                'League' => $league,
                                'Status' => $value->Status,
                                'Season' => $value->Season,
                                'Awayteam' => $value->AwayTeam,
                                'Hometeam' => $value->HomeTeam,
                                'AwayTeamID'=>$value->AwayTeamID,
                                'HomeTeamID' =>$value->HomeTeamID,
                                'AwayTeamName'=>$awayteam->city.' '.$awayteam->name,
                                'HomeTeamName'=>$hometeam->city.' '.$hometeam->name,
                                'DatetimeUTC' => $value->DateTimeUTC,
                                'created_at' => $date->toDateString(),
                            ]);
                        }
                        try {
                            Games::insert($data);
                        } catch (\Exception $e) {
                            //return report($e);
                            return response()->json([
                                'success' => false,
                                'message' =>  report($e),
                            ], 400);
                        }
        
                        $affectedResult = Games::where('created_at','=',$date->toDateString())->where('League','=',$league)->get();
                        if ($affectedResult) {
                            return response()->json([
                                'success' => true,
                                'data' => $affectedResult,
                            ], 200);
                        } else {
                            return response()->json([
                                'success' => false,
                                'data' => null,
                            ], 400);
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'data' => null,
                        ], 400);
                    }
                }
                  else {
                    return response()->json([
                        'success' => false,
                        'data' => $league.' not found.',
                    ], 400);
                  }
            }
    }


    // Bet Request

      public function betRequest(Request $request){
          $data = $request->only('GameID','first_user','second_user','first_user_team','second_user_team','chips','league','service_charge');
          $validator = Validator::make($data, [
            'GameID' => 'required|integer',
            'first_user' => 'required|integer',
            'second_user' => 'required|integer',
            'first_user_team' => 'required|string',
            'second_user_team' => 'required|string',
            'league' => 'required|string',
            'chips'=>'required|string',
            'service_charge'=>'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $gameIDExist = Games::where('GameID',$data['GameID'])->first();
        if(empty($gameIDExist)){
            return response()->json([
                'success' => false,
                'message' => "This Game ID doesn't Exist",
            ], 400);
        }

        $gameTimeExist = Games::where('GameID',$data['GameID'])
                                ->where('DatetimeUTC','>=',Carbon::now())
                                ->first();
        if(empty($gameTimeExist)){
            return response()->json([
                'success' => false,
                'message' => "Deadline Passed.",
            ], 400);
        }
       
        $checkMatchStatus = $this->checkMatchStatus($data['league'],$data['GameID']);
        if(!empty($checkMatchStatus)){
            return response()->json([
                'success' => false,
                'message' => 'This match Status is '.$checkMatchStatus,
            ], 400);
        } 
        
        if(JWTAuth::user()->id == $data['first_user']){
            $betplaced = $this->betalreadyplaced($data['GameID'],JWTAuth::user()->id);
            if(!empty($betplaced)){
                return response()->json([
                    'success' => false,
                    'message' => 'For the Same Game your bet is already placed,Try with another game.',
                ], 400);
            }

            $exist = BetRequest::where(function ($q)  use ($data) {
                $q->where('first_user',$data['first_user'])->orWhere('second_user',$data['first_user']);
            })->where(function ($q) use ($data) {
                $q->where('first_user',$data['second_user'])->orWhere('second_user',$data['second_user']);
            })-> where('GameID',$data['GameID'])
            ->first();
        
            //echo '<pre>';print_r($exist);die;
            if(empty($exist)){
                
                $userWalletAmount = UserWallets::where('user_id',$data['first_user'])->first();
                
                if($userWalletAmount->available_chips<$data['chips']){
                    return response()->json([
                        'success' => false,
                        'message' => "You haven't sufficient Chips in your wallet.",
                    ], 400);
                }

                try{
                    
                    $bets = BetRequest::create([
                        'GameID'=>$data['GameID'],
                        'first_user'=>$data['first_user'],
                        'second_user'=>$data['second_user'],
                        'first_user_team'=>$data['first_user_team'],
                        'second_user_team'=>$data['second_user_team'],
                        'chips'=>$data['chips'],
                        'first_user_sc'=>$data['service_charge'],
                    ]);
                     $total_chips = $bets->chips + $bets->first_user_sc;
                    $this->manageTranscation($bets->id,$bets['first_user'],$bets['second_user'],'debit',$bets->first_user_team.' V '.$bets->second_user_team,'Bet', $total_chips);
                    $this->betRequestNotification($bets->id,$data['first_user'],$data['second_user'],$data['first_user_team'],$data['second_user_team'],$data['chips']);
                    return response()->json([
                        'success' => true,
                        'message' => 'Bet Request is Sent',
                        'data' => $bets
                    ], 200);
    
                } catch(\Exception $e) {
                    //return response($e->getMessage(), 400);
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 400);   
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' =>'You have already sent bet Request to this user for the same Game.',
                ], 400);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login User Id and First User Id are not same.',
            ], 400);
        }
        
      }

      // Add Data into Notification

      public function betRequestNotification($bet_id,$first,$second,$team1,$team2,$chips){
        $firstUser = User::where('id',$first)->first();
        $notify = UserNotification::create([
            'user_id' =>$second,
            'bet_request_id' =>$bet_id,
            'notification_key'=>UserNotification::BET,
            'type' => $team1.' V '.$team2,
            'title' => '@'.$firstUser->username.' sent a',
            'chips' => $chips,
            'image' => $firstUser->image_path,
        ]);

        if($notify){
            $this->fcmNotification($second,$firstUser->username,$team1,$team2);
        } else {
            return false;
        }
      }


      // Sent Notification for Bet Request

      // Fire Base Notification

      public function fcmNotification($user_id,$requestBy,$team1,$team2){
        $title = 'Bet Request';
        $message= '@'.$requestBy.' sent Bet Request to you for this game '.$team1.' V '.$team2;
        try{
            $fcmTokens = User::whereNotNull('fcm_token')->where('id','=',$user_id)->pluck('fcm_token')->toArray();
            //dd($fcmTokens);
            Larafirebase::withTitle($title)
                    ->withBody($message)
                    ->sendNotification($fcmTokens);
        }catch(\Exception $e){
            report($e);
        }
    }

    // Bet request Action

    public function betRequestAction(Request $request){
        $data = $request->only('action','bet_request_id');
        $validator = Validator::make($data, [
            'action' => 'required',
            'bet_request_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], 400);
        }

        $bet = BetRequest::where('id',$data['bet_request_id'])->first();
        if(empty($bet)){
            return response()->json([
                'success' => false,
                'message' => 'This Bet request not available now.',
            ], 400);
        }

        $gameTimeExist = Games::where('GameID',$bet['GameID'])
                                ->where('DatetimeUTC','>=',Carbon::now())
                                ->first();

        if(empty($gameTimeExist)){
            return response()->json([
                'success' => false,
                'message' => "Deadline lost",
            ], 400);
        }

        if($data['action'] == 'Accept'){
            if(JWTAuth::user()->id!=$bet['second_user']){
                return response()->json([
                    'success' => false,
                    'message' => 'Login user Id and Bet against User id are not same.',
                ], 400);
            }

            $betplacedFirst = $this->betalreadyplaced($bet['GameID'],$bet['first_user']);
            
            $betplacedSecond = $this->betalreadyplaced($bet['GameID'],$bet['second_user']);
            
            if(!empty($betplacedFirst) || !empty($betplacedSecond)){
                return response()->json([
                    'success' => false,
                    'message' => 'For the Same Game your bet is already placed,Try with another game.',
                ], 400);
            }

            $userWalletAmount = UserWallets::where('user_id',$bet['second_user'])->first();
                if($userWalletAmount->available_chips<$bet['chips']){
                    return response()->json([
                        'success' => false,
                        'message' => "You haven't sufficient Chips in your wallet.",
                    ], 400);
                }
            
            $ids = [$bet->first_user,$bet->second_user];
            $updateStatus = RandomBet::whereIn('user_id',$ids)->update(['Status' =>1]);
            $affectedRows = BetRequest::where("id",$data['bet_request_id'])->update(["action" => $data['action'],'second_user_sc' => $bet->first_user_sc]);
            BetList::create([
                'GameID' =>$bet->GameID,
                'bet_id' =>$bet->id,
                ]);
                
            $total_chips = $bet->chips + $bet->first_user_sc;
            $this->manageTranscation($bet->id,$bet->second_user,$bet->first_user,'debit',$bet->first_user_team.' V '.$bet->second_user_team,'Bet', $total_chips);
                

            return response()->json([
                    'success' => true,
                    'message' => "Request Accepted Successfully.",
                ], 200);

        } else if($data['action'] == 'Decline'){
            if(JWTAuth::user()->id!=$bet['second_user']){
                return response()->json([
                    'success' => false,
                    'message' => 'Login user Id and Bet against User Id are not same.',
                ], 400);
            }

         $affectedRows = BetRequest::where("id",$data['bet_request_id'])->update(["action" => $data['action']]);
          
         $title = 'Bet Cancel';
          $name = $bet->first_user_team.' V '.$bet->second_user_team;
          $this->manageTranscation($bet->id,$bet->first_user,$bet->second_user,'credit',$name,$title,$bet->chips);

         return response()->json([
            'success' => true,
            'message' => "Request Declined Successfully.",
        ], 200);

        } else if($data['action'] == 'Cancel') {
            if(JWTAuth::user()->id!=$bet['first_user']){
                return response()->json([
                    'success' => false,
                    'message' => "You don't have not access to cancel this request.",
                ], 400);
            }

          $affectedRows = BetRequest::where("id",$data['bet_request_id'])->update(["action" => $data['action']]);
          UserNotification::where('bet_request_id',$data['bet_request_id'])->delete();
          $title = 'Bet Cancel';
          $name = $bet->first_user_team.' V '.$bet->second_user_team;
          $this->manageTranscation($bet->id,$bet->first_user,$bet->second_user,'credit',$name,$title,$bet->chips);

           return response()->json([
                'success' => true,
                'message' => "Request cancelled Successfully.",
            ], 200);

        }

    }
    // Betting History

    public function betHistory(Request $request){
        $user = JWTAuth::user();
        $data = $request->only('second_user');
        $data['first_user'] = $user->id;
        $validator = Validator::make($data, [
            'second_user' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $result = BetRequest::join('bet_list', 'bet_request.id', '=', 'bet_list.bet_id')
                     ->where(function ($q)  use ($data) {
                        $q->where('bet_request.first_user',$data['first_user'])->orWhere('bet_request.second_user',$data['first_user']);
                    })->where(function ($q) use ($data) {
                        $q->where('bet_request.first_user',$data['second_user'])->orWhere('bet_request.second_user',$data['second_user']);
                    })->select('bet_request.first_user_team','bet_request.second_user_team','bet_request.chips','bet_list.result')
                    ->get();

            $user = User::where('id',$data['second_user'])
                          ->select('users.name','users.image_path')
                          ->first();
            $first_user_won = 0;
            $second_user_won = 0;

            foreach($result as $res){
                if($res['first_user_team']==$res['result']){
                    $first_user_won++;
                } else if ($res['second_user_team']==$res['result']){
                    $second_user_won++;
                } else {
                    $first_user_won = 0;
                    $second_user_won = 0;
                }
            }
                $userData = [
                    'second_user_name' => $user->name,
                    'profile_image' => $user->image_path,
                    'no_of_won_first_user' => $first_user_won,
                    'no_of_won_second_user' => $second_user_won
                ];
                      if(count($result)>0){
                        return response()->json([
                            'success' => true,
                            'data' => $result,
                            'user' => $userData
                        ], 200);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => "You haven't played bet with this user.",
                            'user' => $userData
                        ], 200);
                    }
                    
    }

    // Check Final Match Status Before Betting

    public function checkMatchStatus($league,$gameId){
        if($league =='mlb' || $league =='nba'){
            if($league =='mlb'){
                $key = env('MLB_Key')!=null?env('MLB_Key'):Config::get('app.MLB_Key');
                $endpoint = env('API_URL')!=null?env('API_URL'):Config::get('app.API_URL');
                $url = $endpoint.$league.'/stats/json/BoxScore/'.$gameId.'?key='.$key;
                $result =  Http::get($url);
                if ($result->status() == 200) {
                    $data = json_decode($result);
                        if($data->Game->Status !='Scheduled'){
                            $affectedRows = Games::where("GameID",$gameId)
                            ->where("League",$league)
                            ->update(["Status" =>$data->Game->Status]);
                            return $data->Game->Status;
                        }
                }
            } else if($league =='nba'){
                $key = env('NBA_Key')!=null?env('NBA_Key'):Config::get('app.NBA_Key');
                $url = $endpoint.$league.'/stats/json/BoxScore/'.$gameId.'?key='.$key;
                $result =  Http::get($url);
               if ($result->status() == 200) {
                        $data = json_decode($result);
                        if($data->Game->Status !='Scheduled'){
                            $affectedRows = Games::where("GameID",$gameId)->where("League",$league)->update(['Status' => $data->Game->Status]);
                            return $data->Game->Status;
                        }
                } 
            }
        } else if($league == 'nfl'){
            $key = env('NFL_Key')!=null?env('NFL_Key'):Config::get('app.NFL_Key');
            $url = $endpoint.$league.'/stats/json/BoxScoreByScoreIDV3/'.$gameId.'?key='.$key;
            $result =  Http::get($url);
            if ($result->status() == 200) {
                    $data = json_decode($result);
                    if($data->Game->Status !='Scheduled'){
                        $affectedRows = Games::where("GameID",$gameId)
                        ->where("League",$league)
                        ->update(["Status" =>$data->Game->Status]);
                        return $data->Game->Status;
                    }
                
            } 
        }
    }

    // Manage Transcation
    public function manageTranscation($bet_id,$user_id,$against_user,$type,$name,$title,$amount){
        $againstUserData = User::select('username','image_path')->where('id',$against_user)->first();
        $transcation = Transcation::create([
            'bet_id' => $bet_id,
            'user_id' => $user_id,
            'transactionstype' => $type,
            'transactionsname' => '@'.$againstUserData->username,
            'title' => $name,
            'transactionsamount' => $amount,
            'image' => $againstUserData->image_path,
        ]);

        if($type =='credit'){
            $userwallet = UserWallets::where('user_id',$user_id)->first();
            if($userwallet){
                $totalChips = $userwallet->available_chips + $amount;
                $affectedRows = UserWallets::where("user_id", $user_id)->update(["available_chips" => $totalChips]);
            }
            else {
                $wallet = UserWallets::create([
                    'user_id' =>$user_id,
                    'available_chips' =>$amount,
            ]);
            }
        }
        else if($type =='debit') {
            $userwallet = UserWallets::where('user_id',$user_id)->first();
            if($userwallet){
                $totalChips = $userwallet->available_chips - $amount;
                $affectedRows = UserWallets::where("user_id", $user_id)->update(["available_chips" => $totalChips]);
            }
        }
         //$this->manageUserWallet($user_id,$type,$amount);
    }

    // Manage User Wallet 
    public function manageUserWallet($user_id,$type,$amount){
        if($type=='credit'){
            $userwallet = UserWallets::where('user_id',$user_id)->first();
            if($userwallet){
                $totalChips = $userwallet->available_chips + $amount;
                $affectedRows = UserWallets::where("user_id", $user_id)->update(["available_chips" => $totalChips]);
            }
            else {
                $wallet = UserWallets::create([
                    'user_id' =>$user_id,
                    'available_chips' =>$amount,
            ]);
            }
        }
        else if($type=='debit') {
            $userwallet = UserWallets::where('user_id',$user_id)->first();
            if($userwallet){
                return $totalChips = $userwallet->available_chips - $amount;
                $affectedRows = UserWallets::where("user_id", $user_id)->update(["available_chips" => $totalChips]);
            }
        }
    }
    // random Bet Request

    public function randomBetRequest(Request $request){
        $data = $request->only('GameID','League','Team','Chips');
        $user = JWTAuth::user();
        $userWalletAmount = UserWallets::where('user_id',$user->id)->first();
        if($userWalletAmount->available_chips<$data['Chips']){
            return response()->json([
                        'success' => false,
                        'message' => "You haven't sufficient Chips in your wallet.",
            ], 400);
        }
        RandomBet::where('GameID',$data['GameID'])->where('League',$data['League'])->where('user_id',$user->id)->delete();  
        RandomBet::create([
            'user_id'=>$user->id,
            'GameID'=>$data['GameID'],
            'League'=>$data['League'],
            'Team'=>$data['Team'],
            'Chips'=>$data['Chips'],  
        ]);

        $game = Games::where('GameID',$data['GameID'])->where('League',$data['League'])->where('Status','Scheduled')->first();
        
        if(!empty($game)){
            if($data['Team']==$game->Awayteam){
                $againstTeam = $game->Hometeam; 
            } else if($data['Team']==$game->Hometeam){
                $againstTeam = $game->Awayteam;
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "This Game id ".$game->Status,
            ], 400);
        }
    
        $betaganist = RandomBet::where('GameID',$data['GameID'])
                        ->where('League',$data['League'])
                        ->where('Chips',$data['Chips'])
                        ->where('Team',$againstTeam)
                        ->where('user_id','!=',$user->id)
                        ->where('Status',0)
                        ->first();
         //echo '<pre>'; print_r($betaganist);die;               
        if(!empty($betaganist)){
            $users = ['first_user'=>$betaganist->user_id,
                  'second_user' =>$user->id,
                  'GameID' =>$data['GameID']
                ];

                $exist = BetRequest::where(function ($q)  use ($users) {
                    $q->where('first_user',$users['first_user'])->orWhere('second_user',$users['first_user']);
                })->where(function ($q) use ($users) {
                    $q->where('first_user',$users['second_user'])->orWhere('second_user',$users['second_user']);
                })-> where('GameID',$users['GameID'])
                ->first();
            //echo '<pre>'; print_r($exist);die;  
          if(!empty($exist)){
              $betwithNewUser = RandomBet::where('GameID',$data['GameID'])
              ->where('League',$data['League'])
              ->where('Chips',$data['Chips'])
              ->where('Team',$againstTeam)
              ->where('Status',0)
              ->where('user_id','!=',$users['first_user'])
              ->orWhere('user_id','!=',$user->id)
              ->first();
  
              if(!empty($betwithNewUser)){
                  $bets = BetRequest::create([
                      'GameID'=>$data['GameID'],
                      'first_user'=>$betwithNewUser->user_id,
                      'second_user'=>$user->id,
                      'first_user_team'=>$againstTeam,
                      'second_user_team'=>$data['Team'],
                      'chips'=>$data['Chips'],
                      'action'=>'Accept',
                  ]);
                  //echo '<pre>'; print_r($bets);die;
                  BetList::create([
                      'GameID' =>$bets->GameID,
                      'bet_id' =>$bets->id,
                      ]);
                  
                  $ids = [$betwithNewUser->user_id,$user->id];
                  $updateStatus = RandomBet::whereIn('user_id',$ids)->update(['Status' =>1]);
                  $this->manageTranscation($bets->id,$betwithNewUser->user_id,$user->id,'debit',$againstTeam.' V '.$data['Team'],'Bet', $data['Chips']);
                  $this->manageTranscation($bets->id,$user->id,$betwithNewUser->user_id,'debit',$againstTeam.' V '.$data['Team'],'Bet', $data['Chips']);
                //   foreach($ids as $id){
                //     $this->manageTranscation($bets->id,$id,'debit',$againstTeam.' V '.$data['Team'],'Bet', $data['Chips']);
                //   }
                  
                  
  
                  return response()->json([
                      'success' => true,
                      'message' => "Your Bet is placed",
                  ], 200);
      
      
              } 
          }
          else {
                  $bets = BetRequest::create([
                      'GameID'=>$data['GameID'],
                      'first_user'=>$betaganist->user_id,
                      'second_user'=>$user->id,
                      'first_user_team'=>$againstTeam,
                      'second_user_team'=>$data['Team'],
                      'chips'=>$data['Chips'],
                      'action'=>'Accept',
                  ]);
                  //echo '<pre>'; print_r($bets);die;
                  BetList::create([
                      'GameID' =>$bets->GameID,
                      'bet_id' =>$bets->id,
                      ]);
                  
                  $ids = [$betaganist->user_id,$user->id];
                  $updateStatus = RandomBet::whereIn('user_id',$ids)->update(['Status' =>1]);
                  $this->manageTranscation($bets->id,$betaganist->user_id,$user->id,'debit',$againstTeam.' V '.$data['Team'],'Bet', $data['Chips']);
                  $this->manageTranscation($bets->id,$user->id,$betaganist->user_id,'debit',$againstTeam.' V '.$data['Team'],'Bet', $data['Chips']);
                //   foreach($ids as $id){
                //   $this->manageTranscation($bets->id,$id,'debit',$againstTeam.' V '.$data['Team'],'Bet', $data['Chips']);
                // }
                  
                  return response()->json([
                      'success' => true,
                      'message' => "Your Bet is placed",
                  ], 200);
      
      
          }
        }
                
    }

    // random Bet Notification Mark

    // public function randomBetUserNotification($first,$second,$bet_id,$team1,$team2,$chips)
    // {
    //     $firstUser = User::where('id',$first)->first();
    //     $notify = UserNotification::create([
    //         'user_id' =>$second,
    //         'bet_request_id' =>$bet_id,
    //         'notification_key'=>3,
    //         'type' => $team1.' V '.$team2,
    //         'title' => '@'.$firstUser->username.' sent a',
    //         'chips' => $chips,
    //         'image' => $firstUser->image_path,
    //     ]);
    // }


    // Random User

        public function randomUser(Request $request){
            $user_id = JWTAuth::user()->id;
            $user = User::where('id','!=',$user_id)->select('id','name','username','image_path')
                            ->inRandomOrder()
                            ->first();

            
            return response()->json([
                        'success' => true,
                        'data' => $user,
                    ], 200);
        }

        // check bet is already placed or not

        public function betalreadyplaced($gameID,$user_id){
            $data['GameID'] = $gameID;
            $data['user_id'] = $user_id;
            $betplaced = BetRequest::where(function ($q)  use ($data) {
                $q->where('first_user',$data['user_id'])->orWhere('second_user',$data['user_id']);
                })-> where('GameID',$data['GameID'])
            ->where('action','Accept')
            ->first();
            return $betplaced;

        }

        // Bet placed Full Data

        public function betRequestfulldata($bet_id){
            $bet = BetRequest::where('id',$bet_id)->first();
            //echo '<pre>'; print_r($bet->second_user); die;
            if(empty($bet)){
                return response()->json([
                    'success' => false,
                    'message' => "This Bet ID doesn't exist.",
                ], 400);
            }
            $firstUser = User::where('id',$bet->first_user)->select('name')->first();
            $secondUser = User::where('id',$bet->second_user)->select('name')->first();
            $gamesidRecord = Games::where('GameID',$bet->GameID)
                                    ->where('Awayteam',$bet->first_user_team)
                                    ->select('AwayTeamName','HomeTeamName','DatetimeUTC')
                                    ->first();

            if(empty($gamesidRecord)){
                $gamesRecord = Games::where('GameID',$bet->GameID)
                ->where('Hometeam',$bet->first_user_team)
                ->select('AwayTeamName','HomeTeamName','DatetimeUTC')
                ->first();
                $first_user_team = $gamesRecord->HomeTeamName;
                $second_user_team =$gamesRecord->AwayTeamName;
                $game_date = $gamesRecord->DatetimeUTC;
            } else{
                $second_user_team = $gamesidRecord->AwayTeamName;
                $first_user_team = $gamesidRecord->HomeTeamName;
                $game_date = $gamesidRecord->DatetimeUTC;
            }

            $data = [
                'bet_id'=>$bet_id,
                'firstUserName' => $firstUser->name,
                'secondUserName' => $secondUser->name,
                'first_user_team' => $first_user_team,
                'second_user_team' => $second_user_team,
                'wage_amount' => $bet->chips,
                'service_charge' => $bet->first_user_sc,
                'total'=>$bet->chips + $bet->first_user_sc,
                'Game_DateTimeUTC'=>$game_date,
                'Bet_DateTime_created'=>$bet->created_at,
                'status'=>$bet->action,
            ];

                return response()->json([
                    'success' => true,
                    'data' =>$data,
                ], 200);
    

        }


        // Random user with filter data

        public function randomUserDataRequest(Request $request){
            $data = $request->only('game_id','league','user_team','against_team','chips');
            $user_id =JWTAuth::user()->id;
            $getResult = RandomBet::where('user_id',$user_id)
                                    ->where('GameID',$data['game_id'])
                                    ->where('League',$data['league'])
                                    ->where('user_team',$data['user_team'])
                                    ->where('against_team',$data['against_team'])
                                    ->where('Status',0)
                                    ->first();
            if(!empty($getResult)){
                $res=RandomBet::where('user_id',$user_id)
                ->where('GameID',$data['game_id'])
                ->where('League',$data['league'])
                ->where('user_team',$data['user_team'])
                ->where('against_team',$data['against_team'])
                ->where('Status',0)
                ->delete();
            }
                            try{
                                    RandomBet::create([
                                    'user_id'=>$user_id,
                                    'GameID'=>$data['game_id'],
                                    'League'=>$data['league'],
                                    'user_team'=>$data['user_team'],
                                    'against_team'=>$data['against_team'],
                                    'Chips'=>$data['chips'],
                             ]);
                        } catch (\Exception $e) {
                            return response()->json([
                            'success' => false,
                            'message' =>  report($e),
                            ], 400);
                        }

                    $result = RandomBet::join('users','random_bet.user_id','users.id')
                                    ->where('random_bet.Chips',$data['chips'])
                                    ->where('random_bet.GameID',$data['game_id'])
                                    ->where('random_bet.League',$data['league'])
                                    ->where('random_bet.user_team',$data['against_team'])
                                    ->where('random_bet.user_id','!=',$user_id)
                                    ->select('users.id','users.name','users.username','users.image_path')
                                    ->first();
                    if(empty($result)){
                        $perferncewithChip = RandomBet::join('users','random_bet.user_id','users.id')
                                    ->where('random_bet.GameID',$data['game_id'])
                                    ->where('random_bet.League',$data['league'])
                                    ->where('random_bet.user_team',$data['against_team'])
                                    ->where('random_bet.user_id','!=',$user_id)
                                    ->select('users.id','users.name','users.username','users.image_path')
                                    ->first();
                       if(empty($result) && empty($perferncewithChip)){
                         $randomUser = User::where('id','!=',$user_id)->select('id','name','username','image_path')
                                       ->inRandomOrder()
                                       ->first();
                          if(empty($randomUser))
                          {
                            return response()->json([
                                'success' => true,
                                'message' => 'No user available',
                                'data' => 'Data Not Found',
                                ], 200);
                          } else {
                            return response()->json([
                                'success' => true,
                                'data' => $randomUser,
                                ], 200);
                          }
                        } else {
                            return response()->json([
                                'success' => true,
                                'data' => $perferncewithChip,
                                ], 200);
                        }
                    } else {
                        return response()->json([
                            'success' => true,
                            'data' => $result,
                            ], 200);
                    }
                    

        }

        // Revert Back pending request amount 

        public function revertAmountPendingRequest(){
            $games = Games::where('Status','Final')
                            ->where('DatetimeUTC','<=',Carbon::yesterday())
                            ->get();
             
            if(count($games)>0){
                foreach($games as $game){
                    $bets = BetRequest::where('GameID',$game->GameID)
                                       ->where('action','Pending')
                                       ->where('isAmountRevert','0')
                                       ->get();
                   
                    if(count($bets)>0){
                        foreach($bets as $bet){
                            if(empty($bet->second_user_sc)){
                                
                                $updateStatus = BetRequest::where('id',$bet->id)->update(['isAmountRevert' =>1]);
                                $total_chips = $bet->chips + $bet->first_user_sc;
                                $this->manageTranscation($bet->id,$bet->first_user,$bet->second_user,'credit',$bet->first_user_team.' V '.$bet->second_user_team. ' bet is not accepted & match is complete','Bet', $total_chips);
                            }
                        }
                    }
                }
            }
            
        }

            // my bet active or settled

            public function myBetActiveORSettled($status){
                // 1 == Active
                // 0 == Settled
                // Bet List - Bet match result Pending 
                $user = JWTAuth::user()->id;
                if($status ==1){
                    $a = BetRequest::join('bet_list','bet_request.id','=','bet_list.bet_id')
                            ->join('games','bet_request.GameID','=','games.GameID')
                            ->leftjoin('users','bet_request.second_user','=','users.id')
                            ->select('users.username','games.DatetimeUTC','bet_request.second_user_team','bet_request.chips','bet_request.first_user_team','bet_list.bet_id')
                            ->whereNull('bet_list.result')
                            ->where('bet_request.first_user',$user)
                            ->where('bet_request.action','Pending')
                            ->orWhere('bet_request.action','Accept')
                            ->groupBy('bet_list.bet_id')
                            ->get();

                    $b = BetRequest::join('bet_list','bet_request.id','=','bet_list.bet_id')
                            ->join('games','bet_request.GameID','=','games.GameID')
                            ->leftjoin('users','bet_request.first_user','=','users.id')
                            ->select('users.username','games.DatetimeUTC','bet_request.second_user_team','bet_request.chips','bet_request.first_user_team','bet_list.bet_id')
                            ->whereNull('bet_list.result')
                            ->where('bet_request.second_user',$user)
                            ->where('bet_request.action','Pending')
                            ->orWhere('bet_request.action','Accept')
                            ->groupBy('bet_list.bet_id')
                            ->get();

                    $data = $a->union($b);
                        
                   if(!empty($data)){
                    return response()->json([
                        'success' => true,
                        'data' => $data,
                        ], 200);
                   }
                }  
                else if($status == 0){

                    $a = BetList::join('bet_request','bet_list.bet_id','=','bet_request.id')
                            ->join('games','bet_list.GameID','=','games.GameID')
                            ->leftjoin('users','bet_request.second_user','=','users.id')
                            ->select('users.username','games.DatetimeUTC','bet_request.second_user_team','bet_request.chips','bet_request.first_user_team','bet_list.bet_id')
                            ->whereNotNull('bet_list.result')
                            ->where('bet_request.first_user',$user)
                            ->where('bet_request.action','Accept')
                            ->groupBy('bet_list.bet_id')
                            ->get();

                    $b = BetList::join('bet_request','bet_list.bet_id','=','bet_request.id')
                            ->join('games','bet_list.GameID','=','games.GameID')
                            ->leftjoin('users','bet_request.first_user','=','users.id')
                            ->select('users.username','games.DatetimeUTC','bet_request.second_user_team','bet_request.chips','bet_request.first_user_team','bet_list.bet_id')
                            ->whereNotNull('bet_list.result')
                            ->where('bet_request.second_user',$user)
                            ->where('bet_request.action','Accept')
                            ->groupBy('bet_list.bet_id')
                            ->get();

                    $data = $a->union($b);
                        if(!empty($data)){
                                return response()->json([
                                    'success' => true,
                                    'data' => $data,
                                    ], 200);
                        }
                }
            }
}

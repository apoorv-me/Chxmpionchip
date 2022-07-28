<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Kutia\Larafirebase\Facades\Larafirebase;
use App\Models\WalletManagement;
use App\Models\User;
use App\Models\UserWallets;
use App\Models\content;
use App\Models\Contact;
use App\Models\Teams;
use App\Models\Transcation;
use App\Models\UserNotification;
use DB;
use Redirect, File;
use App\Models\Games;
use Carbon\Carbon;
use App\Library\GuzzleTrait;
use App\Mail\WelcomeMail;
use App\Models\BetList;
use App\Models\BetRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Config;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::where('status', '!=', '')->count();
        //return view('home');
        return view('welcome', ["users" => $users]);
    }


    public function emailNotification(Request $request)
    {

        $data = $request->only('email', 'password', 'name', 'username', 'Birth_date', 'confirm_password', 'gender');

        $validator = Validator::make($data, [
            'name' => 'required|string',
            //'last_name' => 'required|string',
            //'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'Birth_date' => 'required|before:18 years ago',
            //'gender' => 'required|string',
            'password' => 'required|string|min:6|max:30',
            'confirm_password'   => 'required|min:6|max:30|same:password',

        ]);
        //echo '<pre>'; print_r($data); die;
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        } else {

            $details = [
                'title' => 'New User notification from ChxmpionChip',
                'body' => 'Name:-' . $data['name'] . "\r\n" . "\r\n" . 'Email:-' . $data['email'] . "\r\n" . 'Birth Date:-' . $data['Birth_date'] . "\r\n"
            ];

            $firstname = strtok($data['name'], ' ');
            $latest = User::latest()->first();
            if(!empty($latest)){
                $insertedId = $latest->id+1;
            } else {
                $insertedId = 1;
            }
            \QrCode::size(500)->format('png')->generate($insertedId, public_path('qr_images/' . $insertedId . '.png'));
             $qrPath = env('APP_URL') . '/qr_images/' . $insertedId . '.png';
            //$image = \QrCode::format('png')->size(200)->errorCorrection('H')->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])->generate('https://techvblogs.com');
                // $output_file = '/public/qr_images/img-' . time() . '.png';
                // Storage::disk('local')->put($output_file, $image);
    
                // $qrPath = env('APP_URL') .$output_file;

            $user = User::create([
                'name' => $data['name'],
                'username' => $data['email'],
                'email' => strtolower($data['email']),
                'Birth_date' => Carbon::parse($data['Birth_date'])->format('Y-m-d'),
                'gender' => 'Prefer not to answer',  //$data['gender']
                'password' => bcrypt($data['password']),
                'qr_code' => $qrPath,
                'verifyDL' => 0,
                'refer_status' => 'registered',
                'referral_code' => uniqid()
            ]);

            try {
                Mail::to('apoorv.shukla@resourcifi.com')->send(new \App\Mail\NotificationMail($details));
                Mail::to($data['email'])->send(new WelcomeMail($user));
            } catch (\Exception $e) {
                //return response($e->getMessage(), 422);
                if ($user) {
                    return redirect()->back()->with('message', $firstname);
                }
            }

            if ($user) {
                $this->manageTranscation($user->id,'credit', 'ChxmpionChip',WalletManagement::REGISTER, WalletManagement::REGISTER_CHIPS);
                $day = date('l', strtotime($user->created_at));
                if($day == 'Friday'){
                  $this->manageTranscation($user->id,'credit', 'ChxmpionChip',WalletManagement::FRIDAY_DEPOSIT, WalletManagement::FRIDAY_DEPOSIT_CHIPS);
                }
                $this->userSignUpNotification($user->id,WalletManagement::REGISTER_CHIPS);
                return redirect()->back()->with('message', $firstname);
            }
        }
    }

    // View File of reset password
    public function viewReset(Request $request)
    {
        return view('user.reset');
    }

    // Cron Job which run on every friday to Update the chips
    public function addChips()
    {
        $users = User::all();
        foreach ($users as $user) {
            $this->manageTranscation($user->id, 'credit', '@ChxmpionChip', WalletManagement::FRIDAY_DEPOSIT, WalletManagement::FRIDAY_DEPOSIT_CHIPS);
        }
    }



    // Manage User Wallet 
    public function manageUserWallet($user_id, $type, $amount)
    {
        if ($type == 'credit') {
            $userwallet = UserWallets::where('user_id', $user_id)->first();
            if ($userwallet) {
                $totalChips = $userwallet->available_chips + $amount;
                $affectedRows = UserWallets::where("user_id", $user_id)->update(["available_chips" => $totalChips]);
            } else {
                $wallet = UserWallets::create([
                    'user_id' => $user_id,
                    'available_chips' => $amount,
                ]);
            }
        } else if ($type == 'debit') {
            $userwallet = UserWallets::where('user_id', $user_id)->first();
            if ($userwallet) {
                $totalChips = $userwallet->available_chips - $amount;
                $affectedRows = UserWallets::where("user_id", $user_id)->update(["available_chips" => $totalChips]);
            }
        }
    }

    public function privacyPolicy(Request $request)
    {
        $data = content::where('page_id', 1)->first(); //1 == Privacy Policy
        return view('privacy-policy', ['data' => $data, 'class' => 'active']);
    }

    public function faq(Request $request)
    {
        $data = content::where('page_id', 1)->first(); //1 == Privacy Policy
        return view('faq', ['data' => $data, 'class' => 'active']);
    }

    public function aboutUs(Request $request)
    {
        $data = content::where('page_id', 2)->first(); //2 == About Us
        return view('about-us', ['data' => $data, 'class' => 'active']);
    }

    public function termOfUse(Request $request)
    {
        $data = content::where('page_id', 3)->first(); //3 == Term of Use
        return view('term-of-use', ['data' => $data, 'class' => 'active']);
    }

    public function contactUs(Request $request)
    {
        return view('contact-us', ['class' => 'active']);
    }

    public function contactUsProcess(Request $request)
    {
        $data = $request->only('name', 'email', 'description');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email',
            'description' => 'required|string'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        //Request is valid, create new user
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description
        ]);

        //User created, return success response
        return redirect()->back()->with('message', ' Thank you for contact Us ! We will get back to you.');
    }

    // Manage Transcation
    public function manageTranscation($user_id, $type, $name, $title, $amount)
    {
        $transcation = Transcation::create([
            'user_id' => $user_id,
            'transactionstype' => $type,
            'transactionsname' => $name,
            'title' => $title,
            'transactionsamount' => $amount,
            'image' => asset('assets/img/chxmpionchip.png'),
        ]);
        $this->manageUserWallet($user_id, $type, $amount);
        $this->userFridyNotification($user_id);
        if ($transcation) {
            return true;
        } else {
            return false;
        }
    }


    // Send Notification

    public function notification($user_id,$title,$message)
    {
        try {
            $fcmTokens = User::whereNotNull('fcm_token')->where('id', '=', $user_id)->pluck('fcm_token')->toArray();
            //Notification::send(null,new SendPushNotification($request->title,$request->message,$fcmTokens));

            /* or */

            //auth()->user()->notify(new SendPushNotification($title,$message,$fcmTokens));

            /* or */
            Larafirebase::withTitle($title)
                ->withBody($message)
                ->sendNotification($fcmTokens);
        } catch (\Exception $e) {
            report($e);
            //return redirect()->back()->with('error','Something goes wrong while sending notification.');
        }
    }

    // User Notification Friday Reward 

    public function userFridyNotification($user_id)
    {
        $notify = UserNotification::create([
            'user_id' => $user_id,
            'notification_key'=>UserNotification::FRIDAY,
            'type' => 'Friday bonus received',
            'title' => '@ChxmpionChip sent ',
            'chips' => WalletManagement::FRIDAY_DEPOSIT_CHIPS,
            'image' => asset('assets/img/chxmpionchip.png'),
        ]);

        if ($notify) {
            $title = 'ChxmpionChip';
            $message = 'Friday Bonus Received';
            $this->notification($user_id,$title,$message);
        } else {
            return false;
        }
    }

    // Cron Job For Gett Sport Data of Current Date


    use GuzzleTrait;
    public function getGamesCron()
    {
        $leagues = array('mlb', 'nba', 'nfl');
        foreach ($leagues as $league) {
            $date = Carbon::now();
            $recordExist = Games::where('created_at', '=', $date->toDateString())->where('League', '=', $league)->get();
            //echo count($recordExist);  
            if (count($recordExist) > 0 && !empty($recordExist)) {
                // return response()->json([
                //     'success' => true,
                //     'data' => $recordExist,
                // ], 200);
            } else {
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
                            
                            $gameExist = Games::where('GameID',$value->GameID)->where('League',$league)->first();
                            if(empty($gameExist)){
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
                        }
                        try {
                            Games::insert($data);
                        } catch (\Exception $e) {
                            //return report($e);
                        }
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
                            
                            $gameExist = Games::where('GameID',$value->GameID)->where('League',$league)->first();
                            if(empty($gameExist)){
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

                            
                        }
                        try {
                            Games::insert($data);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }
    }
   // Game Result
    public function updateGameResult(){
      
       $games = Games::whereNotIn('Status', ['Final','Postponed','Canceled','F/OT'])->get();

       if(!empty($games)){
           foreach($games as $game){
                
               if($game->League == 'mlb'){
                $key = env('MLB_Key')!=null?env('MLB_Key'):Config::get('app.MLB_Key');
                $endpoint = env('API_URL')!=null?env('API_URL'):Config::get('app.API_URL');
                $url = $endpoint.$game->League.'/stats/json/BoxScore/'.$game->GameID.'?key='.$key;
                $result =  Http::get($url);
                if ($result->status() == 200 || $result->status() == 201) {
                    
                    $data = json_decode($result);
                    
                        if($data->Game->Status == 'Final' || $data->Game->Status == 'F/OT'){
                            Log::info('arah aa');
                            $affectedRows = Games::where("GameID",$game->GameID)
                            ->where("League",$game->League)
                            ->update(["Status" =>$data->Game->Status, "AwayTeamScore" =>$data->Game->AwayTeamRuns, "HomeTeamScore"=>$data->Game->HomeTeamRuns]); 
                            
                            $this->updateBetGamesResult($game->League,$game->GameID);
                        }
                        else if($data->Game->Status !='Scheduled'){
                            $affectedRows = Games::where("GameID",$game->GameID)
                            ->where("League",$game->League)
                            ->update(["Status" =>$data->Game->Status]);
                        }
                }
               } else if($game->League =='nba'){
                $key = env('NBA_Key')!=null?env('NBA_Key'):Config::get('app.NBA_Key');
                $endpoint = env('API_URL')!=null?env('API_URL'):Config::get('app.API_URL');
                $url = $endpoint.$game->League.'/stats/json/BoxScore/'.$game->GameID.'?key='.$key;
                $result =  Http::get($url);
                if ($result->status() == 200 || $result->status() == 201) {
                    $data = json_decode($result);
                        if($data->Game->Status == 'Final' || $data->Game->Status == 'F/OT'){
                            $affectedRows = Games::where("GameID",$game->GameID)
                            ->where("League",$game->League)
                            ->update(["Status" =>$data->Game->Status,"AwayTeamScore" =>$data->Game->AwayTeamScore, "HomeTeamScore"=>$data->Game->HomeTeamScore]); 

                            $this->updateBetGamesResult($game->League,$game->GameID);
                        }
                        else if($data->Game->Status !='Scheduled'){
                            $affectedRows = Games::where("GameID",$game->GameID)
                            ->where("League",$game->League)
                            ->update(["Status" =>$data->Game->Status]);
                        }
                }
               } else if($game->League == 'nfl'){
                $key = env('NFL_Key')!=null?env('NFL_Key'):Config::get('app.NFL_Key');
                $endpoint = env('API_URL')!=null?env('API_URL'):Config::get('app.API_URL');
                $url = $endpoint.$game->League.'/stats/json/BoxScoreByScoreIDV3/'.$game->GameID.'?key='.$key;
                $result =  Http::get($url);

                if ($result->status() == 200 || $result->status() == 201) {
                    $data = json_decode($result);
                        if($data->Game->Status == 'Final' || $data->Game->Status == 'F/OT'){
                            $affectedRows = Games::where("GameID",$game->GameID)
                            ->where("League",$game->League)
                            ->update(["Status" =>$data->Game->Status,"AwayTeamScore" =>$data->Game->AwayScore, "HomeTeamScore"=>$data->Game->HomeScore]); 
                            
                            $this->updateBetGamesResult($game->League,$game->GameID);
                        }
                        else if($data->Game->Status !='Scheduled'){
                            $affectedRows = Games::where("GameID",$game->GameID)
                            ->where("League",$game->League)
                            ->update(["Status" =>$data->Game->Status]);
                        }
                }
               }
           }
       }
    }


    // Update Won or Losse Game Result

    public function updateBetGamesResult($league,$gameId){
         
        $game = Games::where('GameID',$gameId)->where('League',$league)->first();
        if(!empty($game)){
            if($game->AwayTeamScore > $game->HomeTeamScore){
                $won = $game->Awayteam;
            } else if($game->HomeTeamScore > $game->AwayTeamScore){
                $won = $game->Hometeam;
            }
            Log::info($game);
            $bet = BetList::where('GameID',$gameId)->first();
            if(!empty($bet)){
                Log::info($bet);
                $affectedRows = BetList::where("GameID",$gameId)
                            ->update(["result" =>$won]);
            $this->sendWinningNotification($gameId,$won);
            }
        }
    }

    // Send Notification to the winner User on Bet Contest
     public function sendWinningNotification($gameId,$won){
            Log::info('send winning notification');
            $bets = BetRequest::where('GameID',$gameId)->where('notifiy',0)->get();
            if(!empty($bets)){
                Log::info($bets);
                foreach($bets as $bet){
                    if($bet->first_user_team==$won){
                        $amountWon = 2*$bet->chips;
                        $total_chips = $bet->chips+$bet->second_user_sc;
                        $title ="You Champion! You're a winner in ".$bet->first_user_team.' V '.$bet->second_user_team ;
                        $message = 'Congratulations ! You won the '.$amountWon;
                    
                        $this->notification($bet->first_user,$title,$message);
                        //dd($bet->first_user);
                        $first_user_name = User::where('id',$bet->first_user)->select('name')->first();
                        $second_user_name = User::where('id',$bet->second_user)->select('name')->first();

                        $total_service_chips = 2*$bet->first_user_sc;
                        WalletManagement::create([
                                        'bet_request_id'=>$bet->id,
                                        'game'=>$bet->first_user_team.' V '.$bet->second_user_team,
                                        'bet_between'=>$first_user_name->name.' V '.$second_user_name->name,
                                        'bet_amount'=>$bet->chips,
                                        'service_charge'=>$bet->second_user_sc,
                                        'total_chips'=>$total_service_chips,
                                        ]);

                        $this->transcation($bet->first_user,'credit',$bet->first_user_team.' V '.$bet->second_user_team,'Bet', $amountWon,$bet->id);
                        $this->transcation($bet->second_user,'debit',$bet->first_user_team.' V '.$bet->second_user_team,'Bet', $total_chips,$bet->id);
                        //$this->serviceCharges($bet->id,$bet->first_user_team.' V '.$bet->second_user_team,$first_user_name->name.' V '.$second_user_name->name,$bet->chips,$bet->second_user_sc);
                    } else if($bet->second_user_team==$won){
                        $amountWon = 2*$bet->chips;
                        $total_chips = $bet->chips+$bet->second_user_sc;
                        $title ="You Champion! You're a winner in ".$bet->first_user_team.' V '.$bet->second_user_team ;
                        $message = 'Congratulations ! You won the '. 2*$bet->chips;
                        //dd($bet->first_user.'ddddf');
                        $first_user_name = User::where('id',$bet->first_user)->select('name')->first();
                        $second_user_name = User::where('id',$bet->second_user)->select('name')->first();

                        $total_service_chips = 2*$bet->second_user_sc;
                        WalletManagement::create([
                                        'bet_request_id'=>$bet->id,
                                        'game'=>$bet->first_user_team.' V '.$bet->second_user_team,
                                        'bet_between'=>$first_user_name->name.' V '.$second_user_name->name,
                                        'bet_amount'=>$bet->chips,
                                        'service_charge'=>$bet->second_user_sc,
                                        'total_chips'=>$total_service_chips,
                                        ]);

                        $this->notification($bet->first_user,$title,$message);
                        //$this->serviceCharges($bet->id,$bet->first_user_team.' V '.$bet->second_user_team,$first_user_name->name.' V '.$second_user_name->name,$bet->chips,$bet->second_user_sc);
                        $this->transcation($bet->first_user,'debit',$bet->first_user_team.' V '.$bet->second_user_team,'Bet', $total_chips,$bet->id);
                        $this->transcation($bet->second_user,'credit',$bet->first_user_team.' V '.$bet->second_user_team,'Bet', $amountWon,$bet->id);
                        
                        
                    }
                    $affectedRows = BetRequest::where("id",$bet->id)
                            ->update(["notifiy" =>1]);
                }
                
            }
     }

       // Manage Transcation 
       public function transcation($user_id,$type,$name,$title,$amount,$betid){
        $transcation = Transcation::create([
            'user_id' => $user_id,
            'transactionstype' => $type,
            'transactionsname' => $name,
            'title' => $title,
            'transactionsamount' => $amount,
            'image' => asset('assets/img/chxmpionchip.png'),
        ]);
        $notify = UserNotification::create([
            'user_id' => $user_id,
            'bet_request_id' => $betid,
            'notification_key'=>UserNotification::BET_RESULT,
            'type' => $type,
            'title' => $name,
            'chips' => $amount,
            'image' => asset('assets/img/chxmpionchip.png'),
        ]);

        $this->manageUserWallet($user_id,$type,$amount);
    }


    // Sign Up Notification  

    public function userSignUpNotification($user_id,$chips){
        $notify = UserNotification::create([
            'user_id' => $user_id,
            'notification_key'=>UserNotification::SIGNUP,
            'type' => null,
            'title' => 'Thanks for signing up! Lets take a tour.',
            'chips' => $chips,
            'image' => asset('assets/img/chxmpionchip.png'),
        ]);

        if($notify){
            return true;
            //$this->notification($user_id);
        } else {
            return false;
        }
    }

    // Get Teams with all different leagues


    public function getTeamsByLeagues(){
        $leagues = ['mlb','nba','nfl'];
        foreach($leagues as $league){
            if($league == 'mlb'){
                $key = env('MLB_Key')!=null?env('MLB_Key'):Config::get('app.MLB_Key');
            } else if($league == 'nba'){
                $key = env('NBA_Key')!=null?env('NBA_Key'):Config::get('app.NBA_Key');
            } else if($league == 'nfl'){
                $key = env('NFL_Key')!=null?env('NFL_Key'):Config::get('app.NFL_Key');
            }
                $endpoint = env('API_URL')!=null?env('API_URL'):Config::get('app.API_URL');
                $url = $endpoint.$league.'/scores/json/AllTeams?key='.$key;
            $teams =  Http::get($url);
            if($teams->status() == 200 || 201){
                foreach(json_decode($teams) as $team){
                    $insertTeams[] = [
                        'team_id'=> $team->TeamID,
                        'league' => $league,
                        'city'=>$team->City,
                        'name'=>$team->Name,
                        'created_at'=>Carbon::now(),
                    ];
                }

                try {
                    Teams::truncate();
                    Teams::insert($insertTeams);
                } catch (\Exception $e) {
                }
            }
        }
        
    }


    // Service charges

    public function serviceCharges($bet_id,$game,$bet_between,$bet_amount,$service_charge){
        
        try{
            $total_chips = 2*$service_charge;
            WalletManagement::create([
                'bet_request_id'=>$bet_id,
                'game'=>$game,
                'bet_between'=>$bet_between,
                'bet_amount'=>$bet_amount,
                'service_charge'=>$service_charge,
                'total_chips'=>$total_chips,
            ]);
        } catch(\Exception $e){

        }
    }
}

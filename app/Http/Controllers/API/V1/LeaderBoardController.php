<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use App\Models\User;
use App\Models\WalletManagement;
use App\Models\UserWallets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Validator;
use App\Models\Transcation;
use App\Models\PromoCodeManagment;
use App\Models\PromoCode;
use App\Models\BetList;
use App\Models\BetRequest;
use Carbon\Carbon;
use DB;

class LeaderBoardController extends Controller
{
    //
    // Top 10 LeaderBoard User on the basis of winning and chips
    public function getLeaderBoardUser(Request $request)
    {
        $user = JWTAuth::user();
        $user_id = $user->id;
        $userwallet = UserWallets::leftjoin('users', 'user_wallets.user_id', '=', 'users.id')
            ->where('user_id', '=', $user_id)
            ->select('user_wallets.available_chips', 'users.image_path', 'users.name', 'users.username', 'users.id as user_id')
            ->get();
        $userRank = $this->getRanking($user_id);
        $top10User = UserWallets::leftjoin('users', 'user_wallets.user_id', '=', 'users.id')
            //->where('user_id', '!=', $user_id)
            ->select('user_wallets.available_chips', 'users.image_path', 'users.name', 'users.username', 'users.id as user_id')
            ->orderBy('user_wallets.available_chips', 'DESC')->take(7)->skip(3)->get();

        if ($userRank == 1 || $userRank == 2 || $userRank == 3) {
            $top3User = UserWallets::leftjoin('users', 'user_wallets.user_id', '=', 'users.id')
                ->select('user_wallets.available_chips', 'users.image_path', 'users.name', 'users.username', 'users.id as user_id')
                ->orderBy('user_wallets.available_chips', 'DESC')->take(3)->skip(0)->get();
        } else {
            $top3User = UserWallets::leftjoin('users', 'user_wallets.user_id', '=', 'users.id')
                ->where('user_id', '!=', $user_id)
                ->select('user_wallets.available_chips', 'users.image_path', 'users.name', 'users.username', 'users.id as user_id')
                ->orderBy('user_wallets.available_chips', 'DESC')->take(3)->skip(0)->get();
        }


        return response()->json([
            'success' => true,
            'user' => isset($userwallet) ? $userwallet : null,
            'userRank' => isset($userRank) ? $userRank : null,
            'top3User' => isset($top3User) ? $top3User : null,
            'leaderboard' => isset($top10User) ? $top10User : null
        ]);
    }

    public function getTranscation(Request $request)
    {
        $user = JWTAuth::user();
        $transcations = Transcation::where('user_id', $user->id)->orderBy('id', 'DESC')->get();
        $pendingRequest = BetRequest::join('transcation','bet_request.id','=','transcation.bet_id')
                                    ->leftjoin('bet_list','bet_request.id','=','bet_list.bet_id')
                                    ->select('transcation.*','bet_request.action')
                                    ->whereNull('bet_list.result')
                                    ->where('bet_request.action','Accept')
                                    ->orWhere('bet_request.action','Pending')
                                    ->where('bet_request.first_user',$user->id)
                                    ->orWhere('bet_request.second_user',$user->id)
                                    ->get();

        $totalTranscation = $transcations->merge($pendingRequest);
        if (!empty($transcations) && !empty($pendingRequest)) {
            return response()->json([
                'success' => true,
                'transcations' => isset($totalTranscation) ? $totalTranscation : null,
            ]);
        } else {
            return response()->json([
                'success' => true,
                'transcations' => null,
            ]);
        }
    }

    // User Wallet

    public function userWallet(Request $request)
    {
        $user = JWTAuth::user();
        $userwallet = UserWallets::where('user_id', $user->id)->first();
        if (!empty($userwallet)) {
            return response()->json([
                'success' => true,
                'wallet' => isset($userwallet->available_chips) ? $userwallet->available_chips : null,
                'message' => 'Chips Available.'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'wallet' => null,
                'message' => 'Chips Not Available.'
            ]);
        }
    }

    // FCM Token

    public function fcmToken(Request $request)
    {
        $data = $request->only('fcm_token', 'platform');
        $user = JWTAuth::user();
        if ($user->fcm_token != $data['fcm_token']) {
            $updateData['fcm_token'] = $data['fcm_token'];
            $updateData['platform'] = $data['platform'];
            $result = $user->update($updateData);
            return response()->json([
                'success' => true,
                'message' => 'Device Token Updated Successfully.'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Device Token are Same.'
            ], Response::HTTP_OK);
        }
    }

    // Rank 

    public function getRanking($user_id)
    {

        $collection = collect(UserWallets::orderBy('available_chips', 'DESC')->get());
        $data       = $collection->where('user_id', $user_id);
        $value      = $data->keys()->first() + 1;
        return $value;
    }

    // Manage Promo Code

    public function promoCodeProcess(Request $request)
    {
        $data = $request->only('promo_code');
        $validator = Validator::make($data, [
            'promo_code' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $user = JWTAuth::user();
        $result = PromoCode::where('name', '=', $data['promo_code'])->where('valid_till', '>=', Carbon::now()->toDateString())->first();
        //echo '<pre>'; print_r($result->id); die;
        if (empty($result)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Promo Code or Valid date Expires.'
            ], 400);
        } else {
            $alreadyUsed = $this->checkPromoAlreadyUsed($user->id,$result->id);
            if($alreadyUsed == 1){
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this promo code.'
                ],400);
            }
            PromoCodeManagment::create([
                'user_id' => $user->id,
                'promo_code_id' =>$result->id,
                'promo_code' => $data['promo_code'],
            ]);

            $this->manageTranscation($user->id, 'credit', '@ChxmpionChip', 'Promo Code', $result->chips);

            return response()->json([
                'success' => true,
                'message' => 'Promo Code enabled now.'
            ], Response::HTTP_OK);
            
        }
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

        try {
            UserNotification::create([
                'user_id' => $user_id,
                'notification_key'=>UserNotification::PROMO_CODE,
                'type' => $type,
                'title' => $name,
                'chips' => $amount,
                'image' => asset('assets/img/chxmpionchip.png'),
            ]);

          } catch (Exception $e) {
                  return $e->getMessage();

          }

        $this->manageUserWallet($user_id, $type, $amount);
        if ($transcation) {
            return true;
        } else {
            return false;
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

    // Check the promo code user already used or not
      public function checkPromoAlreadyUsed($user_id,$promo_id){
        $result = PromoCodeManagment::where('user_id', '=',$user_id)->where('promo_code_id', '=',$promo_id)->first();
        if($result){
            return true;
        } else {
            return false;
        }
      }
}

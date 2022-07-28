<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WalletManagement;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use App\Models\UserWallets;
use App\Models\UserNotification;
use Kutia\Larafirebase\Facades\Larafirebase;
use App\Models\Transcation;
use Validator;
use Socialite;
use Exception;
use Auth;

class SocialController extends Controller
{
    public function facebookRedirect()
    {
        //dd('hello');
        return Socialite::driver('facebook')->redirect();
    }

    public function loginWithFacebook()
    {
        try {
    
            $user = Socialite::driver('facebook')->user();
            dd($user);
            $isUser = User::where('fb_id', $user->id)->first();
     
            if($isUser){
                Auth::login($isUser);
                return redirect('/home');
            }else{
                $createUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'fb_id' => $user->id,
                    'password' => encrypt('admin@123')
                ]);
    
                Auth::login($createUser);
                return redirect('/home');
            }
    
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }


    // Google Login id

    public function loginWithGoogle(Request $request){
        $checkGoogleID = User::where('google_id',$request->google_id)->first();

        $latest = User::latest()->first();
        if(!empty($latest)){
            $insertedId = $latest->id+1;
        } else {
            $insertedId = 1;
        }

        if(!empty($checkGoogleID)){
            $token = $this->authenticate($request->email);
            //User created, return success response
               return response()->json([
                   'success' => true,
                   'message' => 'User created successfully',
                   'access_token' => isset($token)?$token:'',
                   'data' => $user
                   ], Response::HTTP_OK);

        } else {
         \QrCode::size(500)->format('png')->generate($insertedId, public_path('qr_images/'.$insertedId.'.png'));
          $qrPath = env('APP_URL').'/qr_images/'.$insertedId.'.png';

          $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'ssn' => $request->ssn,
            'Birth_date' => $request->Birth_date,
            'gender' => $request->gender,
            'image_path'=>$request->image_path,
            'verifyDL'=>$request->verifyDL,
            'qr_code' => $qrPath,
            'google_id' => $request->google_id,
            'refer_status' => 'registered',
            'referral_code' => uniqid()
        ]);

                $this->manageTranscation($user->id,'credit', 'ChxmpionChip',WalletManagement::REGISTER, WalletManagement::REGISTER_CHIPS);
                $day = date('l', strtotime($user->created_at));
                if($day == 'Friday'){
                  $this->manageTranscation($user->id,'credit', 'ChxmpionChip',WalletManagement::FRIDAY_DEPOSIT, WalletManagement::FRIDAY_DEPOSIT_CHIPS);
                }
                $this->userSignUpNotification($user->id,WalletManagement::REGISTER_CHIPS);
                $token = $this->authenticate($request->email);
             //User created, return success response
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully',
                    'access_token' => isset($token)?$token:'',
                    'data' => $user
                    ], Response::HTTP_OK);


        }
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

    // Manage Transcation
    public function manageTranscation($user_id,$type,$name,$title,$amount){
        $transcation = Transcation::create([
            'user_id' => $user_id,
            'transactionstype' => $type,
            'transactionsname' => $name,
            'title' => $title,
            'transactionsamount' => $amount,
            'image' => asset('assets/img/chxmpionchip.png'),
        ]);
        $this->manageUserWallet($user_id,$type,$amount);
        if($transcation){
            return true;
        } else {
            return false;
        }
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
                $totalChips = $userwallet->available_chips - $amount;
                $affectedRows = UserWallets::where("user_id", $user_id)->update(["available_chips" => $totalChips]);
            }
        }
    }


    public function authenticate($email) { 
        //$email = $request->email;
        $user = User::where('email', '=', $email)->first();
        try { 
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::fromUser($user)) { 
                return response()->json(['error' => 'invalid_credentials'], 401);
            } 
        } catch (JWTException $e) { 
            // something went wrong 
            return response()->json(['error' => 'could_not_create_token'], 500); 
        } 
        // if no errors are encountered we can return a JWT 
        return $token; 
    }
}
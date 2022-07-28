<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    //

    public function forgot(Request $request) {

        $credentials = $request->only('email');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $users = User::where('email', '=', $request->input('email'))->first();
        if ($users === null) {
            return response()->json([
                'success' => false,
                'message' => "This email doesn't exist into our database.Please check your email"

            ], Response::HTTP_NOT_FOUND);
        } else {
            //Password::sendResetLink($credentials);
            $res = $this->sendMail($request->email);
            if($res==1){
                return response()->json([
                'success' => true,
                'message' => 'Check your inbox, we have sent a OTP to reset your password.'
            ], Response::HTTP_OK); 
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Mail is not sent.'
                ], Response::HTTP_NOT_FOUND); 
            }
            
        }
        
    }


    public function sendMail($email){
        $otp = $this->generateToken($email);
        try {
            Mail::to($email)->send(new SendMail($otp));
            return true;
          } catch (Exception $e) {
                  return $e->getMessage();

          }
        
    }

    public function generateToken($email){
      $isOtherToken = DB::table('password_resets')->where('email', $email)->first();

      if($isOtherToken) {
        //return $isOtherToken->token;
        $token = Str::random(80);
        $otp = random_int(1000, 9999);
        $this->updateToken($token,$otp,$email);
        return $otp;
      }

      $token = Str::random(80);
      $otp = random_int(1000, 9999); //Str::random(80);
      $this->storeToken($token,$otp,$email);
      return $otp;
    }

     public function storeToken($token,$otp,$email){
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'otp' =>$otp,
            'created_at' => Carbon::now()            
        ]);
    }

    public function updateToken($token,$otp, $email){
        DB::table('password_resets')
            ->where('email', $email)
            ->update(['token' => $token,'otp' => $otp,'created_at' =>Carbon::now()]);
    }

    public function reset(Request $request) {

        $credentials = $request->only('email','passwordToken','password','password_confirmation');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'passwordToken' => 'required|string',
            'password' => 'required|string|min:6|max:30',
            'password_confirmation'   => 'required|min:6|max:30|same:password',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this->tokenNotFoundError();

    }

    // Verify if token is valid
    private function updatePasswordRow($request){
       return DB::table('password_resets')->where([
           'email' => $request->email,
           'token' => $request->passwordToken
       ]);
    }


    // Token not found response
    private function tokenNotFoundError() {
        return redirect()->back()->with('msg', 'Either your email or token is wrong. Try again to forgot password' );
        // return response()->json([
        //   'error' => 'Either your email or token is wrong.'
        // ],Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // Reset password
    private function resetPassword($request) {
        // find email
        $userData = User::whereEmail($request->email)->first();
        // update password
        $userData->update([
          'password'=>bcrypt($request->password)
        ]);
        // remove verification data from db
        $this->updatePasswordRow($request)->delete();

        // reset password response
        return redirect()->back()->with('msg', 'Password has been updated.' );
        // return response()->json([
        //   'data'=>'Password has been updated.'
        // ],Response::HTTP_CREATED);
    }

    // Change Password 

    public function changePassword(Request $request){
        if (!(Hash::check($request->get('current_password'), JWTAuth::user()->password))) {
            // The passwords matches
            return response()->json([
                'success' => true,
                'message' => 'Your current password does not matches with the old password'
            ], 403);
        }

        if(strcmp($request->get('current_password'), $request->get('new_password')) == 0){
            // Current password and new password same
            return response()->json([
                'success' => true,
                'message' => 'New Password cannot be same as your current password.'
            ], 403);
        }
        //Change Password
            $user = JWTAuth::user();
            $user->password = bcrypt($request->get('new_password'));
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password successfully changed!.'
            ], 200);

    }

    // OTP Verification
    public function otpVerification(Request $request){
        $data = $request->only('email','otp');
        $isToken = DB::table('password_resets')->where('email', $data['email'])->where('otp', $data['otp'])->first();
        if($isToken){
            return response()->json([
                'success' => true,
                'message' => 'OTP is verified',
                'token' =>$isToken->token,
            ],200);
        } else{
            return response()->json([
                'success' => true,
                'message' => 'OTP is wrong! Try again to forgot password'
            ],400);
        }
    }
    

    // Change Password
    public function resetPasswordAPI(Request $request){
        $data = $request->only('email','password','token');
        $userData = User::whereEmail($data['email'])->first();
        
        $isToken = DB::table('password_resets')->where('email', $data['email'])->where('token', $data['token'])->first();

        if($isToken){
            $userData->update([
                'password'=>bcrypt($data['password'])
            ]);

            try {
                if (! $token = JWTAuth::fromUser($userData)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Login credentials are invalid.',
                    ], 400);
                }
            } catch (JWTException $e) {
                return response()->json([
                        'success' => false,
                        'message' => 'Could not create token.',
                    ], 500);
            }

            if($token){
                $friendRequest = User::join('friend_request', 'friend_request.user_id', '=', 'users.id')
                ->where('friend_request.friend_id', $userData->id)
                ->where('friend_request.action','Pending')
                ->get(['users.id as friend_id', 'users.name','friend_request.action']);
                
                if($userData->status=='Active'){
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Password Updated Successfully.',
                        'access_token' => $token,
                        'data' => $userData,
                        'friendRequest' => isset($friendRequest)?$friendRequest:null
                    ]);
                }
                else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account is Inactive from Administrator',
                    ], 500);
                }
            }
        } else{
            return response()->json([
                'success' => true,
                'message' => 'Token Or Email is wrong ! Try again with valid credentials.'
            ],400);
        }

        
    }

}

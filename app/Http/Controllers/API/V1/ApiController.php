<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use App\Models\User;
use App\Models\Contact;
use App\Models\ReplyToContact;
use App\Models\WalletManagement;
use App\Models\ReferralCode;
use App\Models\FriendRequest;
use App\Models\Transcation;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Redirect,File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\UserWallets;
use App\Models\UserNotification;
use Kutia\Larafirebase\Facades\Larafirebase;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('username','email','password','confirm_password','name','Birth_date','ssn','gender','referral_code','verifyDL','country_code','phone');
        $validator = Validator::make($data, [
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'name' => 'required|string',
            'Birth_date' => 'required|date',
            //'ssn' => 'required|string',
            'country_code' => 'required|string',
            'phone' => 'required|string',
            'gender' => 'required|string',
            'password' => 'required|string|min:6|max:50',
            //'confirm_password' => 'required|string|min:6|max:50|same:password'

        ]);
       
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' =>  $validator->messages(),
            ], 400);
        }
           
                $checkEmail = User::where('email',$data['email'])->first();
                $checkUsername = User::where('username',$data['username'])->first();
                if(!empty($checkUsername) || !empty($checkEmail)){

                    if(isset($checkUsername) && isset($checkEmail)){
                        $message = 'Email & Username';
                    }
                    else if(!empty($checkEmail)){
                        $message = 'Email';
                    } else if(!empty($checkUsername)){
                        $message = 'Username';
                    }
                    return response()->json([
                        'success' => false,
                        'message' =>  $message.' already exist ! Try with another '.$message,
                    ], 400);
                } 

        $latest = User::latest()->first();
        if(!empty($latest)){
            $insertedId = $latest->id+1;
        } else {
            $insertedId = 1;
        }
        
        
        \QrCode::size(500)->format('png')->generate($insertedId, public_path('qr_images/'.$insertedId.'.png'));
        $qrPath = env('APP_URL').'/qr_images/'.$insertedId.'.png';
        if(isset($request->referral_code)){
            $refers = User::where('referral_code',$request->referral_code)->select('id')->first();
            //return $refers;
            if(isset($refers)){
                $referrer_id = $refers->id;
                 //Request is valid, create new user
               
                $user = User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    //'ssn' => $request->ssn,
                    'Birth_date' => $request->Birth_date,
                    'gender' => $request->gender,
                    'email' => strtolower($request->email),
                    'password' => bcrypt($request->password),
                    'qr_code' => $qrPath,
                    'country_code' =>$data['country_code'],
                    'phone'=>$data['phone'],
                    'verifyDL' =>$request->verifyDL,
                    'refer_status' => 'referred',
                    'referral_code' => uniqid()
                ]);

                $refers = ReferralCode::create([
                          'referrer_id' =>$referrer_id,
                          'referred_email' =>$request->email
                ]);
                
                
                $this->manageTranscation($user->id,'credit', 'ChxmpionChip',WalletManagement::REGISTER .' '.WalletManagement::REGISTER_WITH_REFERRAL, WalletManagement::REGISTER_WITH_REFERRAL_CHIPS);
                $day = date('l', strtotime($user->created_at));
                if($day == 'Friday'){
                  $this->manageTranscation($user->id,'credit', 'ChxmpionChip',WalletManagement::FRIDAY_DEPOSIT, WalletManagement::FRIDAY_DEPOSIT_CHIPS);
                }
                
                $this->manageTranscation($referrer_id,'credit', 'ChxmpionChip',WalletManagement::REFER, WalletManagement::REFER_CHIPS);
                //User created, return success response
                
                $credentials = ['email'=>$request->email,'password'=>$request->password];
                $token = $this->registerToken($credentials);
                $this->userSignUpNotification($user->id,WalletManagement::FRIDAY_DEPOSIT_CHIPS);
                $userwithNotif = User::leftjoin('user_notification','users.id','=','user_notification.user_id')
                                        ->select('users.*','user_notification.is_read')
                                        ->where('user_notification.is_read','0')
                                        ->where('users.id',$user->id)
                                        ->first();

                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully',
                    'access_token' => isset($token)?$token:'',
                    'data' => $userwithNotif ? $userwithNotif:$user
                    ], Response::HTTP_OK);

            } 
             else {

                return response()->json([
                    'success' => false,
                    'message' => 'Referral Code is Wrong ! Try with Valid Referral Code or Sign up without Referral Code',
                ], 400);

             }
        }
        else {
            //return $request;die;
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                //'ssn' => $request->ssn,
                'Birth_date' => $request->Birth_date,
                'gender' => $request->gender,
                'email' => strtolower($request->email),
                'password' => bcrypt($request->password),
                'qr_code' => $qrPath,
                'country_code' =>$data['country_code'],
                'phone'=>$data['phone'],
                'verifyDL' =>$request->verifyDL,
                'refer_status' => 'registered',
                'referral_code' => uniqid()
            ]);

                $this->manageTranscation($user->id,'credit', 'ChxmpionChip',WalletManagement::REGISTER, WalletManagement::REGISTER_CHIPS);
                $day = date('l', strtotime($user->created_at));
                if($day == 'Friday'){
                  $this->manageTranscation($user->id,'credit', 'ChxmpionChip',WalletManagement::FRIDAY_DEPOSIT, WalletManagement::FRIDAY_DEPOSIT_CHIPS);
                }

                $credentials = ['email'=>$request->email,'password'=>$request->password];
                $token = $this->registerToken($credentials);
                $this->userSignUpNotification($user->id,WalletManagement::REGISTER_CHIPS);
             //User created, return success response

             $userwithNotif = User::leftjoin('user_notification','users.id','=','user_notification.user_id')
                                        ->select('users.*','user_notification.is_read')
                                        ->where('user_notification.is_read','0')
                                        ->where('users.id',$user->id)
                                        ->first();

                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully',
                    'access_token' => isset($token)?$token:'',
                    'data' => $userwithNotif ? $userwithNotif:$user
                    ], Response::HTTP_OK);

        }
       
    }
 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        // $validator = Validator::make($credentials, [
        //     'email' => 'required|email',
        //     'password' => 'required|string|min:6|max:50'
        // ]);

        // //Send failed response if request is not valid
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->messages()], 400);
        // }
        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email or Password incorrect.',
                ], 400);
            }
        } catch (JWTException $e) {
        return $credentials;
            return response()->json([
                    'success' => false,
                    'message' => 'Could not create token.',
                ], 403);
        }
        
        if($token){
            $user = User::where('email',$request->email)->first();
            if($user->status=='Active'){
                $userwithNotif = User::leftjoin('user_notification','users.id','=','user_notification.user_id')
                                        ->select('users.*','user_notification.is_read')
                                        ->where('user_notification.is_read','0')
                                        ->where('users.id',$user->id)
                                        ->first();
                                     
                return response()->json([
                    'success' => true,
                    'access_token' => $token,
                    'data' => $userwithNotif ? $userwithNotif:$user,
                ]);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is Inactive from Administrator',
                ], 403);
            }
        }
        //Token created, return with success response and jwt token
       
    }
 
        //  Login with PIN


    public function loginPin(Request $request){
        
        $credentials = $request->only('email', 'pin');

        //valid credential
        // $validator = Validator::make($credentials, [
        //     'email' => 'required|email',
        //     'pin' => 'required|string|min:4|max:50'
        // ]);

        //Send failed response if request is not valid
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->messages()], 400);
        // }

        //Request is validated
        //Crean token
         $user = User::where('email','=',$request->email)->first();
         if(empty($user)){
            return response()->json([
                'success' => false,
                'message' => 'Email not exist into our database.',
            ], 400);
         }
         
        if (!Hash::check($request->pin, $user->pin, [])) {
            return response()->json([
                'success' => false,
                'message' => 'Email or PIN incorrect.',
            ], 400);
        } 


        try {
            if (! $token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                    'success' => false,
                    'message' => 'Could not create token.',
                ],403);
        }
        
        if($token){
            if($user->status=='Active'){
                $userwithNotif = User::leftjoin('user_notification','users.id','=','user_notification.user_id')
                                        ->select('users.*','user_notification.is_read')
                                        ->where('user_notification.is_read','0')
                                        ->where('users.id',$user->id)
                                        ->first();

                return response()->json([
                    'success' => true,
                    'access_token' => $token,
                    'data' => $userwithNotif ? $userwithNotif:$user,
                ],200);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is Inactive from Administrator',
                ], 403);
            }
        }
        //Token created, return with success response and jwt token
    }


    // User Register Token
    public function registerToken($credentials)
    {
    
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                    'success' => false,
                    'message' => 'Could not create token.',
                ], 403);
        }
        
        if($token){
            return $token;
        }
        //Token created, return with success response and jwt token
       
    }

    // End
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ],200);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    // Login user info
    public function get_user(Request $request)
    {
        // $this->validate($request, [
        //     'token' => 'required'
        // ]);
 
        $user = JWTAuth::authenticate($request->token);
        
        try {
            if (! $token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                    'success' => false,
                    'message' => 'Could not create token.',
                ], 403);
        }
        
        if($token){
            
            if($user->status=='Active'){
                $userwithNotif = User::leftjoin('user_notification','users.id','=','user_notification.user_id')
                                        ->select('users.*','user_notification.is_read')
                                        ->where('user_notification.is_read','0')
                                        ->where('users.id',$user->id)
                                        ->first();

                return response()->json([
                    'success' => true,
                    'access_token' => $token,
                    'data' => $userwithNotif ? $userwithNotif:$user,
                ],200);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is Inactive from Administrator',
                ], 403);
            }
        }

        //return response()->json(['user' => $user]);
    }

    // List Of ALL User 

    public function allUser(Request $request)
    {
        
        $user = User::all();
        return response()->json(['user' => $user]);
    }

    // Contact Us 

    public function contact(Request $request){

        $data = $request->only('name', 'email','description');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email',
            'description' => 'required|string'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Request is valid, create new user
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description
        ]);

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Thank you ! Our Team will get back to you.',
            'data' => $contact
        ], Response::HTTP_OK);

    }

    // Contact Query Response
    public function queryResponse($id){

        if(empty($id)){
            return response()->json([
                'error' => true,
                'message' => 'Query Id is required'
            ], Response::HTTP_OK);
        }
      
        $response = ReplyToContact::where('contact_id',$id)->get();
        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Response from Admin.',
            'data' => $response
        ], Response::HTTP_OK);

    }


    // Profile Update 

    public function profileUpdate(Request $request){
        //return 'hrllo';
        $data = $request->only('name', 'email', 'Birth_date','gender','phoneNumber','fileContents','pin','fcm_token','ssn');
        // $validator = Validator::make($data, [
        //     'name' => 'string',
        //     'email' => 'email',
        //     'Birth_date' => 'string',
        //     'gender' => 'string',
        //     'pin' => 'string',
        //     'phoneNumber' =>'numeric',
        //     'fileContents' => 'string'
        // ]);
        //Send failed response if request is not valid
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->messages()], 409);
        // }
        $user = JWTAuth::user();
        if(isset($request->name)){
            $updateData['name'] = $request->name;
        }
        // if(isset($request->email)){
        //     $updateData['email'] = $request->email;
        // }
        if(isset($request->gender)){
            $updateData['gender'] = $request->gender;
        }
        if(isset($request->Birth_date)){
            $updateData['Birth_date'] = $request->Birth_date;
        }
        if(isset($request->phoneNumber)){
            $updateData['phoneNumber'] = $request->phoneNumber;
        }
        if(isset($request->pin)){
            $updateData['pin'] = bcrypt($request->pin);
        }
        if(isset($request->fcm_token)){
            $updateData['fcm_token'] = $request->fcm_token;
        }
        if(isset($request->platform)){
            $updateData['platform'] = $request->platform;
        }

        if(isset($request->ssn)){
            $updateData['ssn'] = $request->ssn;
        }

        if (isset($data['fileContents'])) {
            $image = $data['fileContents'];
            $image_ext = explode(',',$image);
            $image = str_replace($image_ext[0], '', $image);
            $image = str_replace(' ', '+', $image);
            $name = Str::random(15).'.'.'png';
            $destinationPath = 'public/profile_images/'.$user->id; // 
            $image_base64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$data['fileContents'])); 
            $file = $destinationPath .'/'.$name;
            Storage::put($file,$image_base64);
            $updateData['image_path'] = env('APP_URL').'/storage/profile_images/'.$user->id.'/'.$name;
      }
 
        //Request is valid, update user
        $result = $user->update($updateData); 
        //User updated, return success response
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ], Response::HTTP_OK);

    }

     // Unique Username Or Email

        public function checkUsernameOrEmail(Request $request){

            $data = $request->only('username','email','referral_code');
                if(isset($data['email'])){
                    $checkEmail = User::where('email',$data['email'])->first();
                } else{
                    $checkEmail = '';
                }
               
                if(isset($data['username'])){
                    $checkUsername = User::where('username',$data['username'])->first();
                } else{
                    $checkUsername = '';
                }

                if(isset($data['referral_code'])){
                    $referral_code = User::where('referral_code',$data['referral_code'])->first();
                } else {
                    $referral_code = true;
                }
                
                
                if(!empty($checkUsername) || !empty($checkEmail) || empty($referral_code)){

                    if(isset($checkUsername) && isset($checkEmail) && empty($referral_code)){
                        $message = 'Email,Username & Referral Code';
                        return response()->json([
                            'success' => false,
                            'message' =>  'Email or Username already exist and Referral Code is wrong ! Try with valid credentials',
                        ], 400);
                    }
                    if(isset($checkUsername) && isset($checkEmail) && empty($referral_code)){
                        return response()->json([
                            'success' => false,
                            'message' =>  'Email or Username already exist and Referral Code is wrong ! Try with valid credentials',
                        ], 400);
                    }
                    if(isset($checkUsername) && isset($checkEmail)){
                        return response()->json([
                            'success' => false,
                            'message' =>  'Email or Username already exist! Try with valid credentials',
                        ], 400);
                    }
                    if(isset($checkUsername) && empty($referral_code)){
                        return response()->json([
                            'success' => false,
                            'message' =>  'Username already exist and Referral Code is wrong ! Try with valid credentials',
                        ], 400);
                    }
                    if(isset($checkEmail) && empty($referral_code)){
                        return response()->json([
                            'success' => false,
                            'message' =>  'Email already exist and Referral Code is wrong ! Try with valid credentials',
                        ], 400);
                    }
                    else if(!empty($checkEmail)){
                        $message = 'Email';
                        return response()->json([
                            'success' => false,
                            'message' =>  "Email Taken.",
                        ], 400);
                    } else if(!empty($checkUsername)){
                        $message = 'Username';
                        return response()->json([
                            'success' => false,
                            'message' =>  "Username Taken.",
                        ], 400);
                    }
                    else if(empty($referral_code)){
                        return response()->json([
                            'success' => false,
                            'message' =>  "This Referral Code desn't exist in our database ! try with valid referral code.",
                        ], 400);
                    }
                    
                } 


                return response()->json([
                    'success' => true,
                    'message' => 'Valid Information.'
                ], Response::HTTP_OK);

        }


        // Refresh 
        public function refreshToken(){
                $token = JWTAuth::getToken();
                if(!$token){
                    return response()->json([
                        'success' => true,
                        'message' => 'Token not provided.'
                    ],403);
                }

            try{
                 $token = JWTAuth::refresh($token);
                }catch(TokenInvalidException $e){
                    return response()->json([
                        'success' => true,
                        'message' => 'The token is invalid.'
                    ],403);
                }
                return response()->json([
                    'success' => true,
                    'access_token' => $token
                ], Response::HTTP_OK);
            }

        // Verify DL

        public function dlVerification(Request $request){
            
            $data = $request->only('verifyDL');
            $updateData['verifyDL'] = $data['verifyDL'];
            $user = JWTAuth::user();
            $result = $user->update($updateData); 
    
            return response()->json([
                    'success' => true,
                    'message' => 'DL verified successfully'
                ], Response::HTTP_OK);


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

        // Fire Base Notification

        public function notification($user_id){
            $title = 'ChxmpionChip';
            $message= 'Thanks for signing up! Lets take a tour';
            try{
                $fcmTokens = User::whereNotNull('fcm_token')->where('id','=',$user_id)->pluck('fcm_token')->toArray();
                //Notification::send(null,new SendPushNotification($request->title,$request->message,$fcmTokens));
        
                /* or */
        
                //auth()->user()->notify(new SendPushNotification($title,$message,$fcmTokens));
        
                /* or */
        
                Larafirebase::withTitle($title)
                    ->withBody($message)
                    ->sendMessage($fcmTokens);
        
                //return redirect()->back()->with('success','Notification Sent Successfully!!');
        
            }catch(\Exception $e){
                report($e);
                //return redirect()->back()->with('error','Something goes wrong while sending notification.');
            }
        }

        // Reffered Code Add Amount After login user

        public function referalCodeAddAmount(Request $request){
            $data = $request->only('code');
            $user = JWTAuth::user()->id;
            
            $checkCode = User::where('referral_code')->first();
            $ids = array();
            if(!empty($checkCode)){
                $refers = ReferralCode::create([
                    'referrer_id' =>$checkCode->id,
                    'referred_email' =>$checkCode->email
                 ]);
                 $affectedRows = User::where("id", $user)->update(["refer_status" => 'referred']);

                $this->manageTranscation($user,'credit', 'ChxmpionChip',WalletManagement::REGISTER .' '.WalletManagement::REGISTER_WITH_REFERRAL, WalletManagement::REGISTER_WITH_REFERRAL_CHIPS);
                $this->manageTranscation($checkCode->id,'credit', 'ChxmpionChip',WalletManagement::REFER, WalletManagement::REFER_CHIPS);
                return response()->json([
                    'success' => true,
                    'message' => 'Referral code successfully used.'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid Referral Code. "
                ], 400);
            }
        }
}
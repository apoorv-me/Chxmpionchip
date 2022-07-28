<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Redirect,File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\UserNotification;

class NotificationController extends Controller
{
    //
        public function getUserNotification(Request $request){
            $user = JWTAuth::user();
            $listOfNotification = UserNotification::where('user_id',$user->id)->orderBy('id', 'DESC')->get();
            return response()->json([
                'success' => true,
                'notification' => isset($listOfNotification)?$listOfNotification:null,
            ]);
        }

        // Update Read Status

        public function isReadNotification(Request $request){
            $user = JWTAuth::user();
            $id = $request->id;
            $affectedRows = UserNotification::where('id',$id)
                                              ->where('user_id',$user->id)
                                              ->update(['is_read'=>1]);
            if($affectedRows){
                return response()->json([
                    'success' => true,
                    'notification' =>'Notification Read',
                ]);
            }
            
        }
}

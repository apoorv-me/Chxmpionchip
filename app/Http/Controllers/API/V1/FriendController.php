<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FriendRequest;
use App\Models\FriendList;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Kutia\Larafirebase\Facades\Larafirebase;
use App\Models\UserNotification;
use Config;

class FriendController extends Controller
{
    //

    public function friendRequest(Request $request)
    {
        $data = $request->only('friend_id');
        $user = JWTAuth::user();

        $validator = Validator::make($data, [
            //'user_id' => 'required|string',
            'friend_id' => 'required|string',
        ]);
        //echo '<pre>'; print_r($data); die;
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        } else {
            $data['user_id'] = $user->id;
            //$friendExist =  FriendRequest::where('friend_id', $data['friend_id'])->where('user_id',$user->id)->first();
            $friendExist = FriendRequest::where(function ($q)  use ($data) {
                $q->where('friend_id',$data['friend_id'])->orWhere('user_id',$data['friend_id']);
            })->where(function ($q) use ($data) {
                $q->where('friend_id',$data['user_id'])->orWhere('user_id',$data['user_id']);
            })
            ->first();

            if (!empty($friendExist)) {
                return response()->json([
                    'success' => false,
                    'message' => "Friend Request is already in pending."
                ], 400);
            }

            if ($user->id != $data['friend_id']) {

                FriendRequest::create([
                    'user_id' => $user->id,
                    'friend_id' => $data['friend_id'],
                    'action' => 'Pending',
                    'is_request' => 1,
                    'is_pending' => 1,
                ]);

                $title = 'Friend Request';
                UserNotification::create([
                    'user_id' => $data['friend_id'],
                    'friend_request_id' =>$user->id, 
                    'notification_key'=>UserNotification::FRIEND,
                    'type' =>$title,
                    'title' =>$user->username.' sent friend request to you.',
                    'image' => $user->image_path,
                ]);

                
                $this->sentFriendRequestNotification($data['friend_id'], $user->id, $title);
                return response()->json([
                    'success' => true,
                    'message' => 'Your Request is Pending'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "You can't sent friend request to themselves."
                ], 400);
            }
        }
    }

    // Friend Request Response Accept or  Decline

    public function friendRequestResponse(Request $request)
    {
        $data = $request->only('friend_id', 'action');
        $user = JWTAuth::user();

        $validator = Validator::make($data, [
            //'user_id' => 'required|string',
            'friend_id' => 'required|string',
            'action' => 'required|string'
        ]);
        //echo '<pre>'; print_r($data); die;
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        } else {

            $affectedRows = FriendRequest::where("friend_id", $user->id)->where('user_id', $data['friend_id'])->update(["action" => $data['action'],'is_pending' =>0,'is_request' =>0]);

            if ($data['action'] == 'Accept') {
                FriendList::create([
                    'user_id' => $user->id,
                    'friend_id' => $data['friend_id'],
                    'status' => 'Friend'
                ]);
                FriendList::create([
                    'user_id' => $data['friend_id'],
                    'friend_id' =>$user->id,
                    'status' => 'Friend'
                ]);

                $title = 'Friend Request ' . $data['action'];
                $this->sentFriendRequestNotification($user->id, $data['friend_id'], $title);
            } else if($data['action'] == 'Decline' || $data['action'] == 'Unfriend'){

                //$affectedRows = FriendRequest::where("friend_id", $user->id)->where('user_id', $data['friend_id'])->update(["action" => $data['action'],'is_pending' =>0,'is_request' =>0]);
                //$affectedRows = FriendRequest::where("friend_id", $user->id)->where('user_id', $data['friend_id'])->delete();
                FriendRequest::where(function ($query) use ($user, $data) {
                    $query->where('friend_id', '=', $user->id)
                          ->orWhere('user_id', '=', $user->id);
                })->where(function ($query) use ($user, $data) {
                    $query->where('friend_id', '=', $data['friend_id'])
                          ->orWhere('user_id', '=', $data['friend_id']);
                })->delete();
                
                FriendList::where(function ($query) use ($user, $data) {
                    $query->where('friend_id', '=', $user->id)
                          ->orWhere('user_id', '=', $user->id);
                })->where(function ($query) use ($user, $data) {
                    $query->where('friend_id', '=', $data['friend_id'])
                          ->orWhere('user_id', '=', $data['friend_id']);
                })->delete();

                    
                $title = 'Friend Request ' . $data['action'];
                $this->sentFriendRequestNotification($user->id, $data['friend_id'], $title);
            }


            return response()->json([
                'success' => true,
                'message' => 'You have ' . $data['action'] . ' this Request'
            ], Response::HTTP_OK);
        }
    }

    // Search User By Name

    public function search($name)
    {
        $user = JWTAuth::user();
        $result = User::where('username', 'LIKE', '%' . $name . '%')
            ->leftjoin('friend_list', 'users.id', '=', 'friend_list.friend_id')
            ->where('users.id', '!=', $user->id)
            ->select('users.id', 'users.username', 'users.name', 'users.image_path', 'friend_list.status')
            ->groupBy('friend_list.friend_id')
            ->get();
        if (count($result)) {
            return response()->json([
                'success' => true,
                'Result' => $result
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'Result' => 'No Data not found'
            ], 404);
        }
    }

    // Get User Info 

    public function getUserInfo(Request $request, $id)
    {
        $user = JWTAuth::user();
        if($user->id == $id){
            $result = User::leftjoin('friend_request', 'users.id', '=', 'friend_request.user_id')
            ->select('users.id','users.username','users.name', 'users.email', 'users.image_path', 'friend_request.action','friend_request.is_pending')
            ->where('users.id', $id)
            ->first();
        } else if ($user->id != $id){
            $record1 = friendRequest::where('user_id',$user->id)->where('friend_id',$id)->first();
            $record2 = friendRequest::where('user_id',$id)->where('friend_id',$user->id)->first();
            if(!empty($record1)){
                
            $result = friendRequest::leftjoin('users', 'users.id', '=', 'friend_request.friend_id')
            ->select('users.id','users.username','users.name', 'users.email', 'users.image_path', 'friend_request.action','friend_request.is_pending')
            ->where('friend_request.friend_id', $id)
            ->where('friend_request.user_id', $user->id)
            ->first();
            } else if(!empty($record2)){
                
                $result = friendRequest::leftjoin('users', 'users.id', '=', 'friend_request.user_id')
                ->select('users.id','users.username','users.name', 'users.email', 'users.image_path', 'friend_request.action','friend_request.is_request')
                ->where('friend_request.friend_id', $user->id)
                ->where('friend_request.user_id', $id)
                ->first(); 
            } else {
             $result = User::leftjoin('friend_request', 'users.id', '=', 'friend_request.user_id')
            ->select('users.id','users.username','users.name', 'users.email', 'users.image_path','friend_request.is_pending')
            ->where('users.id', $id)
            ->first();
            }
            
        }
        

        //FriendList::where()
        if (!empty($result)) {
            return response()->json([
                'success' => true,
                'Result' => $result
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'Result' => 'Data Not Found.'
            ], 404);
        }
    }

    // Get User have pending request

    public function searchPending(Request $request)
    {
        $user = JWTAuth::user();
        $result = friendRequest::leftjoin('users', 'friend_request.friend_id', '=', 'users.id')
            ->select('users.name', 'users.email', 'users.id as friend_id', 'users.image_path', 'users.qr_code')
            ->where('friend_request.user_id', $user->id)->where('friend_request.action', '=', 'Pending')
            ->get();
        if (count($result)) {
            return response()->json([
                'success' => true,
                'Result' => $result
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'Result' => 'Data Not Found.'
            ], 404);
        }
    }


    // Get User have Accept request

    public function searchAccept(Request $request)
    {
        $user = JWTAuth::user();
        $result = FriendList::leftjoin('users', 'friend_list.friend_id', '=', 'users.id')
            ->select('users.id','users.username','users.name', 'users.email', 'users.id as friend_id', 'users.image_path', 'users.qr_code', 'friend_list.status')
            ->where('friend_list.user_id', $user->id)
            ->get();

        //FriendList::where()
        if (count($result)) {
            return response()->json([
                'success' => true,
                'Result' => $result
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'Result' => 'Data Not Found.'
            ], 404);
        }
    }

    // Manage Friend Request Sent Notification

    public function sentFriendRequestNotification($friend_id, $user_id, $title)
    {
        $friend = User::select('name', 'username', 'email', 'image_path')->where('id', $friend_id);
        $message = $friend;
        try {
            $fcmTokens = User::whereNotNull('fcm_token')->where('id', '=', $user_id)->pluck('fcm_token')->toArray();
            Larafirebase::withTitle($title)
                ->withBody($message)
                ->sendMessage($fcmTokens);
        } catch (\Exception $e) {
            report($e);
        }
    }
}

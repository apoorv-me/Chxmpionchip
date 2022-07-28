<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserWallets;
use App\Models\WalletManagement;
use App\Models\Admin;
use App\Models\pages;
use App\Models\content;
use App\Models\Contact;
use App\Models\ReplyToContact;
use App\Models\PromoCode;
use App\Models\Notifications;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Redirect;
use Session;
use DB;
use App\DataTables\UsersDataTable;
use App\DataTables\WalletDataTable;
use App\DataTables\AdminWalletDataTable;
use App\DataTables\NotificationDataTable;
use App\DataTables\ContactDataTable;
use App\Jobs\ProcessPromoCode;
use Kutia\Larafirebase\Facades\Larafirebase;

class AdminController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $users = User::where('status', '!=', '')->count();
        return view("admin.dashboard", ["users" => $users]);
    }

    public function user()
    {
        $user = auth()->guard('admin')->user();
        $admin = Admin::where('id', $user->id)->first();
        return view("admin.user", ["admin" => $admin]);
    }

    public function list(UsersDataTable $dataTable)
    {
        return $dataTable->render('admin.tables');
    }

    // Update Profile

    public function updateProfile(Request $request)
    {
        $data = $request->only('name', 'id');
        $validator = Validator::make($data, [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        $affectedRows = Admin::where("id", $data['id'])->update(["name" => $data['name']]);
        if ($affectedRows) {
            return redirect()->back()->with('message', 'Profile Updated Successfully');
        }
    }

    // Change Password
    public function changePassword(Request $request)
    {
        $data = $request->only('password', 'confirm_password', 'id');
        $validator = Validator::make($data, [
            'password' => 'required|string|min:6|max:30',
            'confirm_password'   => 'required|min:6|max:30|same:password',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        $affectedRows = Admin::where("id", $data['id'])->update(["password" => bcrypt($data['password'])]);
        if ($affectedRows) {
            return redirect()->back()->with('msg', 'Password Updated Successfully');
        }
    }

    // User Delete

    public function userDelete(Request $request)
    {
        $data = $request->only('id');
        //User updated, return success response
        UserWallets::where('user_id', $data['id'])->delete();

        $res = User::where('id', $data['id'])->delete();
        if ($res) {
            return response()->json([
                'success' => true,
                'message' => 'User Deleted successfully'
            ], Response::HTTP_OK);
        }
    }

    // update Satus

    public function userStatus(Request $request)
    {
        $data = $request->only('id');
        $result = User::find($data['id']);
        //User updated, return success response
        if ($result['status'] == 'Active') {
            $status = 'Inactive';
        } else {
            $status = 'Active';
        }
        $res = User::where('id', $data['id'])->update(['status' => $status]);
        if ($res) {
            return response()->json([
                'success' => true,
                'message' => 'status updated successfully',
                'data' => $status
            ], Response::HTTP_OK);
        }
    }

    // Content Management

    public function contentManagement()
    {
        $pages = pages::all();
        return view("admin.content", ["pages" => $pages]);
    }

    // Get Content 
    public function getContent($id)
    {

        $data = content::where('page_id', $id)->get()->toArray();

        if (!empty($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'data' => '']);
        }
    }

    // Add or Update Content Management

    public function addUpdateContent(Request $request)
    {
        $data = $request->only('content', 'page_id', 'Page');
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'Page' => 'required',
            'page_id' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        } else {
            $result = content::where('page_id', $data['Page'])->first();
            if (!empty($result)) {
                $affectedRows = content::find($data['Page']);
                $affectedRows->content = $data['content'];
                $affectedRows->save();
                return redirect()->back()->with('message', 'Updated Successfully');
            } else {
                content::create(request()->all());
                return redirect()->back()->with('message', 'Added Successfully');
            }
        }
    }

    // Get Contact Us

    public function contactUs(ContactDataTable $dataTable)
    {

        return $dataTable->render('admin.contact');
    }

    // Reply to the Contact

    public function replyContact(Request $request)
    {
        $data = $request->only('contact_id', 'description');
        $validator = Validator::make($data, [
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        } else {
            ReplyToContact::create(request()->all());
            return redirect()->back()->with('message', 'Added Successfully');
        }
    }

    // View Contact and Reply

    public function getContact($id)
    {
        $contactMsg = Contact::find($id);
        $replyMsg = ReplyToContact::where('contact_id', $id)->get();
        return view("admin.contactView", ["contactMsg" => $contactMsg, "replyMsgs" => $replyMsg]);
    }


    // View Contact and Reply

    public function notification(NotificationDataTable $dataTable)
    {

        return $dataTable->render('admin.notification');
    }

    // View to add New Notification

    public function addNotification()
    {
        return view("admin.add-notification");
    }

    // Process Notification

    public function processNotification(Request $request)
    {
        $data = $request->only('text', 'file');
        $validator = Validator::make($data, [
            'text' => 'required|string',
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        } else {
            if (isset($request->text)) {
                $insertData['text'] = $request->text;
            }

            if ($files = $request->file('file')) {
                $destinationPath = public_path('/notification/'); // 
                $Image = date('YmdHis') . "." . $files->getClientOriginalExtension();
                $files->move($destinationPath, $Image);
                // $image_base64 = base64_encode(file_get_contents($request->file('file')));
                $insertData['image_path'] = env('APP_URL') . '/notification/' . $Image;
                // $insertData['image_base64'] = $image_base64;
            }
            Notifications::create($insertData);
            return redirect()->back()->with('message', 'Added Successfully');
        }
    }
    


    // User Wallet Managment

    public function walletManagement(WalletDataTable $dataTable)
    {

        return $dataTable->render('admin.wallet');
    }

    // Promo Code

    public function promoCode()
    {
        $promoCodes = PromoCode::all();
        return view("admin.promocode", ["promoCodes" => $promoCodes]);
    }

    // view Add Promo Code

    public function addPromoCode()
    {

        return view("admin.add-promocode");
    }

    // Add Process of PromoCode

    public function promoCodeProcess(Request $request)
    {
        $data = $request->only('name', 'chips', 'valid_till');
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:promo_code',
            'chips' => 'required|numeric',
            'valid_till' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        PromoCode::create([
            'name' => $data['name'],
            'chips' => $data['chips'],
            'valid_till' => $data['valid_till'],
        ]);

        return redirect()->back()->with('message', 'You have added Promo Code Successfully');
    }

    // Delete Promo Code

    public function promoCodeDeleteProcess(Request $request)
    {
        $data = $request->only('id');

        $res = PromoCode::where('id', $data['id'])->delete();
        if ($res) {
            return response()->json([
                'success' => true,
                'message' => 'Promo Code Delete successfully'
            ], Response::HTTP_OK);
        }
    }

    // Send Promo Code Mail to All User 

    public function sendPromoCode(Request $request)
    {
        $data = $request->only('promoCode');
        $details = [
            'title' => 'Promo Code from ChxmpionChip',
            'body' => 'Promo Code:-' . $data['promoCode']
        ];

        try {
            $users = User::where('status', 'Active')->select('email')->get();

            $title = 'Promo Code ChxmpionChip';
            $message = 'Use this ' . $details['body'] . ' to add more chips !';
            $fcmTokens = User::whereNotNull('fcm_token')->where('status', '=', 'Active')->pluck('fcm_token')->toArray();
            //dd($fcmTokens);
            Larafirebase::withTitle($title)->withBody($message)->sendNotification($fcmTokens);


            foreach ($users as $user) {
                $details['mail'] = $user->email;
                dispatch(new ProcessPromoCode($details));
            }
            return response()->json([
                'success' => true,
                'message' => 'Promo Code Added In the Queue Successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            //return response($e->getMessage(), 422);
            return response()->json([
                'success' => true,
                'message' => 'SomeThing Went Wrong!'
            ], Response::HTTP_OK);
        }
    }

    // Admin Wallet & Service Charges

    public function adminWalletSC(AdminWalletDataTable $dataTable){
        return $dataTable->render('admin.admin-wallet');
    }
}

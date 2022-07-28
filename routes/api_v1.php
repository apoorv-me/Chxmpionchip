<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\ApiController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\ForgotPasswordController;
use App\Http\Controllers\API\V1\FriendController;
use App\Http\Controllers\API\V1\LeaderBoardController;
use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\SportDataController;
use App\Http\Controllers\API\V1\SocialController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login',        [ApiController::class, 'authenticate']);
Route::post('login-pin',    [ApiController::class, 'loginPin']);
Route::post('unique',       [ApiController::class, 'checkUsernameOrEmail']);
Route::post('register',     [ApiController::class, 'register']);
Route::get('users',         [ApiController::class, 'allUser']);
Route::post('contact',      [ApiController::class, 'contact']);
Route::get('contact/{id}',  [ApiController::class, 'queryResponse']);

// Social Controller login
Route::post('login-with-google',[SocialController::class,'loginWithGoogle']);

// Forgot Password
Route::post('forgot-password',  [ForgotPasswordController::class, 'forgot']);
Route::post('otp-verification', [ForgotPasswordController::class, 'otpVerification']);
Route::post('reset-password',   [ForgotPasswordController::class, 'resetPasswordAPI']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('logout',            [ApiController::class, 'logout']);
    Route::get('user',              [ApiController::class, 'get_user']);
    Route::post('profile-update',   [ApiController::class, 'profileUpdate']);
    Route::post('dl-verification',  [ApiController::class, 'dlVerification']);
    Route::get('refresh',           [ApiController::class, 'refreshToken']);

    // Forgot Password
    Route::post('change-password',  [ForgotPasswordController::class, 'changePassword']);
    Route::post('friend-request',   [FriendController::class, 'friendRequest']);
    Route::post('friend-request-response',  [FriendController::class, 'friendRequestResponse']);
    Route::get('search/{name}',  [FriendController::class, 'search']);
    Route::get('pending-request', [FriendController::class, 'searchPending']);
    Route::get('friends',        [FriendController::class, 'searchAccept']);
    Route::get('user/{id}',        [FriendController::class, 'getUserInfo']);
    // LeaderBoard

    Route::get('leaderboard',       [LeaderBoardController::class, 'getLeaderBoardUser']);
    Route::get('transcations',      [LeaderBoardController::class, 'getTranscation']);
    Route::get('user-wallet',       [LeaderBoardController::class, 'userWallet']);
    Route::post('fcm-token',        [LeaderBoardController::class, 'fcmToken']);

    // Notification
    Route::get('user-notification', [NotificationController::class, 'getUserNotification']);
    // IS Read 
    Route::post('read-notification', [NotificationController::class, 'isReadNotification']);

    // Sport Data API Integration
    Route::get('game-by-date/{league}', [SportDataController::class, 'getGameByDate']);
    Route::post('bet-request', [SportDataController::class, 'betRequest']);
    Route::post('bet-request-action', [SportDataController::class, 'betRequestAction']);
    Route::post('bet-history', [SportDataController::class, 'betHistory']);
    Route::get('my-bet/{status}', [SportDataController::class, 'myBetActiveORSettled']);
    // Promo Code
    Route::post('promo-code', [LeaderBoardController::class, 'promoCodeProcess']);

    // Random Bet
    Route::post('random-bet-request', [SportDataController::class, 'randomBetRequest']);

    // Random User
    Route::get('random-user', [SportDataController::class, 'randomUser']);
    //
    Route::get('data-against-bet/{bet_id}', [SportDataController::class, 'betRequestfulldata']);

    // Randome USer with filter data

    Route::post('random-user-data', [SportDataController::class, 'randomUserDataRequest']);
    // Reffered Code Chips Add
    Route::post('referal-code',[ApiController::class, 'referalCodeAddAmount']);
    
});

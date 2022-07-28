<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\API\V1\SportDataController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('test', fn () => phpinfo() );
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('landing');
Route::view('forgot_password', 'auth.reset_password')->name('password.reset');
Route::post('emailNotification', [App\Http\Controllers\HomeController::class, 'emailNotification'])->name('emailNotification');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('reset', [App\Http\Controllers\HomeController::class, 'viewReset'])->name('viewReset');
Route::post('password/reset', [App\Http\Controllers\API\V1\ForgotPasswordController::class, 'reset'])->name('password.reset');
Route::get('add-chips', [App\Http\Controllers\HomeController::class, 'addChips'])->name('addChips');
Route::get('update-users-wallet', [App\Http\Controllers\HomeController::class, 'updateUserChips'])->name('updateUserChips');


// Public Pages
Route::get('about-us', [HomeController::class, 'aboutUs'])->name('aboutUs');
Route::get('term-of-use', [HomeController::class, 'termOfUse'])->name('termOfUse');
Route::get('privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('contact-us', [HomeController::class, 'contactUs']);
Route::post('contact-us-process', [HomeController::class, 'contactUsProcess'])->name('contactUsProcess');
Route::get('fire-base',[HomeController::class, 'notification'])->name('notification');
// Cron job Url
Route::get('games-cron',[HomeController::class, 'getGamesCron'])->name('getGamesCron');
Route::get('games-status',[HomeController::class, 'updateGameResult'])->name('updateGameResult');
Route::get('game-result/{league}/{gameId}',[HomeController::class, 'updateBetGamesResult'])->name('updateBetGamesResult');
Route::get('game-notifiy/{gameId}/{won}',[HomeController::class, 'sendWinningNotification'])->name('sendWinningNotification');
Route::get('teams-by-leagues',[HomeController::class, 'getTeamsByLeagues'])->name('getTeamsByLeagues');
Route::get('faq',[HomeController::class, 'faq'])->name('faq');
// Cron job end Url
// Route::get('userFridyNotification',[HomeController::class, 'userFridyNotification'])->name('userFridyNotification');
//Route::get('fcm/{user_id}/{requestBy}/{team1}/{team2}',[SportDataController::class, 'fcmNotification']);
Route::get('match-status/{league}/{gameId}',[SportDataController::class, 'checkMatchStatus']);
Route::get('revert-amount-pending-request',[SportDataController::class, 'revertAmountPendingRequest']);
// Route::get('qr-code-g', function () {
  
//     \QrCode::size(500)
//             ->format('png')
//             ->backgroundColor(255,255,255,1)
//             ->generate('ItSolutionStuff.com', public_path('images/qrcode-2.png'));
    
//   return view('qrCode');
    
// });


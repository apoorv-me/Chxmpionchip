<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::get('admin/login', [App\Http\Controllers\Auth\AdminAuthController::class, 'getLogin'])->name('adminLogin');
Route::post('admin/login', [App\Http\Controllers\Auth\AdminAuthController::class, 'postLogin'])->name('adminLoginPost');
Route::get('admin/logout', [App\Http\Controllers\Auth\AdminAuthController::class, 'logout'])->name('adminLogout');
Route::get('admin/forgot-password', [App\Http\Controllers\Auth\AdminAuthController::class, 'forgotPassword'])->name('adminForgotPassword');

Route::group(['prefix' => 'admin', 'middleware' => 'adminauth'], function () {
	// Admin Dashboard
	Route::get('dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
	Route::get('user', [App\Http\Controllers\AdminController::class, 'user'])->name('user');
	Route::get('list', [App\Http\Controllers\AdminController::class, 'list'])->name('list');
	Route::post('profile', [App\Http\Controllers\AdminController::class, 'updateProfile'])->name('profile');
	Route::post('change-password', [App\Http\Controllers\AdminController::class, 'changePassword'])->name('change-password');
	Route::post('user-permanent-delete', [App\Http\Controllers\AdminController::class, 'userDelete'])->name('user.permanent_delete');
	Route::post('user-status', [App\Http\Controllers\AdminController::class, 'userStatus'])->name('user.change_status');
	Route::get('content-management', [App\Http\Controllers\AdminController::class, 'contentManagement'])->name('contentManagement');
	Route::get('get-content/{id}', [App\Http\Controllers\AdminController::class, 'getContent'])->name('getContent');
	Route::post('content', [App\Http\Controllers\AdminController::class, 'addUpdateContent'])->name('addUpdateContent');
	Route::get('contact-us', [App\Http\Controllers\AdminController::class, 'contactUs'])->name('contactUs');
	Route::post('reply', [App\Http\Controllers\AdminController::class, 'replyContact'])->name('replyContact');
	Route::get('contact/{id}', [App\Http\Controllers\AdminController::class, 'getContact'])->name('getContact');
	Route::get('notification', [App\Http\Controllers\AdminController::class, 'notification'])->name('notification');
	Route::get('add-notification', [App\Http\Controllers\AdminController::class, 'addNotification'])->name('addNotification');
	Route::post('process-notification', [App\Http\Controllers\AdminController::class, 'processNotification'])->name('processNotification');
	Route::post('send-notification', [App\Http\Controllers\AdminController::class, 'sendNotification'])->name('sendNotification');
	Route::get('wallet-management', [App\Http\Controllers\AdminController::class, 'walletManagement'])->name('walletManagement');
	Route::get('promo-code', [App\Http\Controllers\AdminController::class, 'promoCode'])->name('promoCode');
	Route::get('add-promo-code', [App\Http\Controllers\AdminController::class, 'addPromoCode'])->name('addPromoCode');
	Route::post('promoCode-process', [App\Http\Controllers\AdminController::class, 'promoCodeProcess'])->name('promoCodeProcess');
	Route::post('delete-promoCode-process', [App\Http\Controllers\AdminController::class, 'promoCodeDeleteProcess'])->name('promoCodeDeleteProcess');
	Route::post('send-promoCode-process', [App\Http\Controllers\AdminController::class, 'sendPromoCode'])->name('promoCodeSendProcess');
	Route::get('admin-wallet', [App\Http\Controllers\AdminController::class, 'adminWalletSC'])->name('adminWalletSC');
});

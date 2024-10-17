<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('public/auth')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'loginUser'])->name('api.login');
    Route::post('/create_account', [AuthenticationController::class, 'createAccount'])->name('api.createAccount');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resendVerification'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});
Route::prefix('public')->group(function (){
   Route::get('/donations', [DonationController::class, 'getAllDonations'])->name('api.getAllDonations');
});

Route::middleware('auth:sanctum')->group( function () {
     Route::prefix('protected')->group(function (){
         Route::post('/auth/logout', [AuthenticationController::class, 'logout'])->name('api.logout');
     });

    Route::prefix('protected')->group(function (){
        Route::get('/user/donations', [DonationController::class, 'getUserDonations'])->name('api.getUserDonations');
        Route::post('user/donations/create', [DonationController::class, 'createDonation'])->name('api.createDonation');
        Route::get('/user/donations/{id}', [DonationController::class, 'showDonation'])->name('api.showDonation');
        Route::put('user/donations/{id}/update', [DonationController::class, 'updateDonation'])->name('api.updateDonation');
    });

});

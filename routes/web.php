<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\Auth\RegistrationController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('/user')->group(function(){
    Route::get('/register', [RegistrationController::class, 'showForm'])->name('user.registration');
    Route::post('/register-new-user', [RegistrationController::class, 'register'])->name('user.creation');
    Route::get('/verify', [RegistrationController::class,'verifyUser'])->name('verify.user');
});
    
Route::post('/verify-otp', [OTPController::class, 'verifyOTP'])->name('verify.otp');
Route::get('/resend-opt', [OTPController::class, 'regenarateOTP'])->name('resend.otp');

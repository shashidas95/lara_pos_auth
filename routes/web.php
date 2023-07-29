<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVerificationMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return view('layout.app');
});
Route::post('/user-registration', [UserController::class, 'UserRegistration']);
Route::post('/user-login', [UserController::class, 'UserLogin']);
Route::post('/send-otp', [UserController::class, 'SendOTPToEMail']);
Route::post('/verify-otp', [UserController::class, 'OTPVerify']);
//Token verify
Route::post('/reset-password', [UserController::class, 'SetPassword'])->middleware(TokenVerificationMiddleware::class);
//logout
Route::get('/user-logout', [UserController::class, 'UserLogout']);
Route::post('/user-profile', [UserController::class, 'UserProfile'])->middleware(TokenVerificationMiddleware::class);
Route::post('/user-update', [UserController::class, 'UserUpdate'])->middleware(TokenVerificationMiddleware::class);



//pages routes
Route::get('/userRegistration', [UserController::class, 'UserRegistrationPage']);
Route::get('/userLogin', [UserController::class, 'UserLoginPage']);
Route::get('/sendOtp', [UserController::class, 'SendOTPCodePage']);
Route::get('/verifyOtp', [UserController::class, 'VerifyOTPPage']);
Route::get('/resetPassword', [UserController::class, 'ResetPasswordPage'])->middleware(TokenVerificationMiddleware::class);

Route::get('/userProfile', [UserController::class, 'ProfilePage'])->middleware(TokenVerificationMiddleware::class);
//after auth
Route::get('/dashboard', [UserController::class, 'DashboardPage'])->middleware(TokenVerificationMiddleware::class);

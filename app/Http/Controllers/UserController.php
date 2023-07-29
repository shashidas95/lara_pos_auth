<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use Firebase\JWT\JWT;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function UserRegistrationPage()
    {
        return view('pages.auth.registration-page');
    }
    public function UserLoginPage()
    {
        return view('pages.auth.login-page');
    }
    public function SendOTPCodePage()
    {
        return view('pages.auth.send-otp-page');
    }
    public function VerifyOTPPage()
    {
        return view('pages.auth.verify-otp-page');
    }
    public function ResetPasswordPage()
    {
        return view('pages.auth.reset-pass-page');
    }
    public function DashboardPage()
    {
        return view('pages.dashboard.dashboard-page');
    }
    public function ProfilePage()
    {
        return view('pages.dashboard.profile-page');
    }

    public function UserRegistration(Request $request)
    {
        try {
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'mobile' => $request->input('mobile'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);
            return response()->json([
                'status' => "success",
                'message' => "User registration successful"
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => "fail",
                'message' => "User registration Failed"
                // 'message' => $e->getMessage()
            ], 401);
        }
    }
    public function UserLogin(Request $request)
    {
        $count = User::where('email', '=', $request->input('email'))
            ->where('password', '=', $request->input('password'))
            ->select('id')->first();
        if ($count !== null) {
            $token = JWTToken::CreateToken($request->input('email'), $count->id);
            return response()->json([
                'status' => "success", 'message' => 'User Logged in successfully',
                // 'token'=>$token //for sending  through body instead of cookie
            ], 200)->cookie('token', $token, 60 * 60 * 24);
        } else {
            return response()->json(['status' => "fail", 'message' => "unauthorised"], 401);
        }
    }
    public function SendOTPToEMail(Request $request)
    {
        $userEmail = $request->input('email');
        $otp = rand(1000, 9999);
        $count = User::where('email', '=', $userEmail)->count();
        if ($count == 1) {
            Mail::to($userEmail)->send(new OTPMail($otp));
            User::where('email', '=', $userEmail)->update(['otp' => $otp]);
            return response()->json(['status' => "success", 'message' => "OTP sent to your Email"], 200);
        } else {
            return response()->json(['status' => "fail", 'message' => "unauthorised"], 401);
        }
    }
    public function OTPVerify(Request $request)
    {
        $userEmail = $request->input('email');
        $otp = $request->input('otp');
        $count = User::where('email', '=', $userEmail)->where('otp', '=', $otp)->count();
        if ($count === 1) {
            User::where('email', '=', $userEmail)->update(['otp' => '0']);
            $token = JWTToken::CreateTokenForSetPassword($request->input('email'));
            return response()->json(['status' => "success", 'message' => "OTP Verified successfully"], 200)->cookie('token', $token, 60 * 60 * 24);
        } else {
            return response()->json(['status' => "fail", 'message' => "unauthorised"], 401);
        }
    }
    public function SetPassword(Request $request)
    {
        try {
            $email = $request->header('email');
            $password = $request->input('password');
            User::where('email', '=', $email)->update(['password' => $password]);
            // User::where($request->input())->update(['password' => $request->input('password')]);

            return response()->json(['status' => "success", 'message' => "password updated succesfully"], 200);
        } catch (Exception $e) {
            return response()->json(['status' => "fail", 'message' => "unauthorised"], 401);
        }
    }
    public function userLogout(Request $request)
    {
        return redirect('/userLogin')->cookie('token', '', -1);
    }
    public function UserProfile(Request $request)
    {
        $email = $request->header('email');
        $user = User::where('email', '=', $email)->first();
        return response()->json([
            'status' => "success",
            'message' => "Request succesfully",
            'data' => $user
        ], 200);
    }
    public function UserUpdate(Request $request)
    {
        $email = $request->header('email');
        User::where('email', '=', $email)->update([
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName'),
            'mobile' => $request->input('mobile'),
            'password' => $request->input('password')
        ]);
        return response()->json([
            'status' => "success",
            'message' => "User profile Updated succesfully",
        ], 200);
    }
}

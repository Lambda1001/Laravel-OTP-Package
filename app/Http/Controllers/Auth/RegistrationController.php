<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\OTPRecord;
use Illuminate\Http\Request;
use App\Services\OTPServices;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function showForm(){
        return view('auth.registration');
    }

    public function verifyUser(){
        return view('auth.verify');
    }

    public function register(Request $request){
        //Validate User info
        $validatedData = $request->validate([
            'full_names' => 'required|string|max:50',
            'email_address' => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $new_user = User::create([
                'name' => $validatedData['full_names'],
                'email' => $validatedData['email_address'],
                'phone_number' => $validatedData['phone_number'],
                'password' => bcrypt($validatedData['password']),
            ]);

            if($new_user){
                Auth::login($new_user);
                $otp_services = new OTPServices;
                $created_otp = $otp_services->sendOTP();

                OTPRecord::create([
                    'user_id' => $new_user->id,
                    'phone_number' => $new_user->phone_number,
                    'otp_code' => bcrypt($created_otp['otp']),
                    'ttl' => $created_otp['ttl'],
                ]);
                return redirect()->route('verify.user');
            }
        } catch (\Throwable $th) {
            Log::error("Err during registration", [$th->getMessage()]);
            return back()->with('Error during registration process. Try again.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\OTPServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class OTPController extends Controller
{
    public $otpService;
    public $maxAttempts = 5;
    public $expiryMinutes = 10;
    public $identifier = 'phone';

    public function __construct(){
        $this->otpService = new OTPServices();
    }

    public function verifyOTP(Request $request){
        try{

            $verified_otp = $this->otpService->verifyOTP($request);

            Log::info('OTP ',$verified_otp);

            if($verified_otp['success'] == false){
                return response()->json([
                    "Response"=> $verified_otp, 
                    'Message'=>"OTP Verification Failed"
                ], 404);
            }

            return response()->json([
                "Response"=> $verified_otp, 
                "message"=>"OTP Verification Successfully"
            ], 200);
        } catch (Exception $e) {
            Log::info('Error: '.$e->getMessage());
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 400);
        }
    }

    public function regenarateOTP(){
        try {
            $otp_message = $this->otpService->sendOTP();
            return response()->json([
                'message' => 'OTP sent to your phone number successfully',
            ]);
        } catch (\Throwable $th) {
            Log::info("Error in regenarating OTP: ".$th->getMessage());
            return response()->json([
                'message' => 'OTP regeneration failed',
            ]);
        }
    }

    protected function getCacheKey($identifier)
    {
        return 'otp: '.md5($identifier);
    }
}

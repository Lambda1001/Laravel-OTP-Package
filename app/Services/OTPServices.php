<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class OTPServices{

    public $ttl = 600;
    public $maxAttempts = 5;
    public $expiryMinutes = 10;
    public $identifier = 'phone';

    public function createOTP(){
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);    

        return $otp;
    }

    public function sendOTP(){
        $user = Auth::user();

        if(!$user){
            return[
                'message' => 'OTP creation failed',
            ];
        }

        $created_otp = $this->createOTP();
        $ttl = $this->ttl;
        if($created_otp){
            $cacheKey = $this->getCacheKey($this->identifier);

            Cache::put($cacheKey, [
                'otp' => bcrypt($created_otp),
                'attempts' => 0,
                'created_at' => now(),
            ], now()->addMinutes(10));

            Log::info($created_otp);
            // $this->sendSMS($created_otp, $user->phone_number);

            return[
                'otp' => $created_otp,
                'ttl' => $this->ttl,
                'message' => 'OTP Created and sent successfully',
            ];
        }
    }

    public function verifyOTP(Request $request){
        $validatedData = Validator::make($request->all(),[
            'otp-code' => 'required|numeric|digits:6',
        ]);

        if ($validatedData->fails()) {
            return[
                'success' => false,
                'errors' => $validatedData->errors()
            ];
        }
        $otp_code = $request->input('otp-code');

        try {
            $cacheKey = $this->getCacheKey($this->identifier);
            $data = Cache::get($cacheKey);

            if(!$data){
                throw new Exception('OTP expired or not found');
            }

            if($data['attempts'] >= $this->maxAttempts){
                Cache::forget($cacheKey);
                throw new Exception('Maximum verification attempts exceeded.');
            }

            if(!Hash::check($otp_code,$data['otp'])){
                $data['attempts']++;
                Cache::put($cacheKey, $data, now()->addMinutes($this->expiryMinutes));

                $remaining_attempts = $this->maxAttempts - $data['attempts'];
                throw new Exception("Invalid OTP. {$remaining_attempts} attempts remaining.");
            }

            Cache::forget($cacheKey);
            return [
                'success' => true,
                'message' => 'OTP verified successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }


    private function sendSMS($otp ,$phone_number){
        $basic = new \Vonage\Client\Credentials\Basic("082dea00", env('VONAGE_API_ACCOUNT_SECRET'));
        $minutes = floor($this->ttl/60);
        $message = "{$otp} is your verification code. It will expire after $minutes minutes. Do not share this code with anyone.";
        Log::info($message);

        $client = new \Vonage\Client($basic);
        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($phone_number, 'APEX RACING', $message)
        );

        $message = $response->current();

        if($message->getStatus() == 0){
            Log::debug("Message to ".$phone_number." has been sent successfully");
        }else{
            Log::error("The message failed with status: ".$message->getStatus());
        }
    }

    protected function getCacheKey($identifier)
    {
        return 'otp: '.md5($identifier);
    }
}


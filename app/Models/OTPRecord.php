<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTPRecord extends Model
{
    protected $table = 'otp_records';
    protected $fillable = [
        'user_id',
        'phone_number',
        'otp_code',
        'ttl',
    ];
}

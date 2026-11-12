<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalKeyEmailOtp extends Model
{
    use HasFactory;

    protected $table = 'digital_key_email_otps';

    protected $fillable = [
        'user_id',
        'email',
        'otp_hash',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user login record associated with this OTP.
     */
    public function userLogin()
    {
        return $this->belongsTo(
            DigitalKeyUserLogin::class,
            'user_id',
            'id'
        );
    }
}

<?php

namespace App\Repositories;

use App\Models\DigitalKeyKey;
use App\Models\DigitalKeyUserLogin;
use App\Models\DigitalKeyEmailOtp;
use Illuminate\Support\Facades\Schema;

class AuthRepository
{
    /**
     * Find key by hash or direct key string
     */
    public function findKey(string $key)
    {
        $hash = hash('sha256', $key);

        return DigitalKeyKey::where('key_hash', $hash)
            ->orWhere('key_hash', $key)
            ->first();
    }



    /**
     * Get user login by key_id
     */
    public function getUserLoginByKeyId(?int $keyId)
    {
        if (!$keyId) {
            return null;
        }

        return DigitalKeyUserLogin::where('key_id', $keyId)
            ->latest('id')
            ->first();
    }

    /**
     * Find user login by email and key_id
     */
    public function saveOtp(array $data)
    {
        $email = strtolower(trim($data['email']));
        $otp = trim((string) $data['otp']);

        $userLogin = DigitalKeyUserLogin::where('email', $email)->latest('id')->first();

        if (!$userLogin) {
            return null;
        }

        // Invalidate any existing OTP records for this user/email
        DigitalKeyEmailOtp::where('email', $email)
            ->orWhere('user_id', $userLogin->id)
            ->delete();

        $otpHash = hash('sha256', $otp);
        $expiresAt = now()->addMinutes(10); // OTP valid for 10 minutes

        $otpstring = DigitalKeyEmailOtp::create([
            'user_id'    => $userLogin->id,
            'email'      => $email,
            'otp_hash'   => $otpHash,
            'expires_at' => $expiresAt,
        ]);

        return $otpstring;
    }

    /**
     * Verify OTP against database record
     */
    public function verifyOtp(string $email, string $otp): bool
    {
        $email = strtolower(trim($email));
        $otp = trim((string) $otp);
        $otpHash = hash('sha256', $otp);

        $record = DigitalKeyEmailOtp::where('email', $email)
            ->where('otp_hash', $otpHash)
            ->where('expires_at', '>=', now())
            ->first();

        if ($record) {
            $record->delete();
            return true;
        }

        return false;
    }

    /**
     * Increment key usage count
     */
    public function incrementKeyUse(int $keyId): void
    {
        DigitalKeyKey::where('key_id', $keyId)->increment('use_count');
    }

    /**
     * Check if email exists in user login table
     */
    public function checkEmailExists(string $email): bool
    {
        return DigitalKeyUserLogin::where('email', $email)->exists();
    }

    /**
     * Store or update user login details
     */
    public function saveUserLogin(array $data)
    {
        return DigitalKeyUserLogin::updateOrCreate(
            [
                'key_id' => $data['key_id'] ?? 0,
            ],
            [
                'email'          => $data['email'],
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'],
                'first_login'    => $data['first_login'] ?? true,
                'first_login_at' => now(),
                'login_status'   => 'active',
            ]
        );
    }
}

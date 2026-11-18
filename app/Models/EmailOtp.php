<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailOtp extends Model
{
    use HasFactory;

    protected $table = 'email_otps';

    protected $fillable = [
        'email',
        'otp',
        'action',
        'payload',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    public function markVerified(): void
    {
        $this->update(['verified_at' => now()]);
    }

    /**
     * Generate or renew an OTP for a given email and action.
     */
    public static function createOtp(string $email, string $action = 'register', ?array $payload = null, int $validMinutes = 10): self
    {
        // Invalidate any previous unverified OTPs for this email and action
        self::where('email', $email)
            ->where('action', $action)
            ->whereNull('verified_at')
            ->delete();

        // 6-digit random code
        $otp = (string) mt_rand(100000, 999999);

        return self::create([
            'email' => $email,
            'otp' => $otp,
            'action' => $action,
            'payload' => $payload,
            'expires_at' => now()->addMinutes($validMinutes),
        ]);
    }

    /**
     * Verify an OTP for a given email and action.
     */
    public static function verifyOtp(string $email, string $otp, string $action = 'register'): ?self
    {
        $record = self::where('email', $email)
            ->where('action', $action)
            ->where('otp', trim($otp))
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if ($record) {
            $record->markVerified();
            return $record;
        }

        return null;
    }
}

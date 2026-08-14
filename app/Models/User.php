<?php

namespace App\Models;

use App\Mail\OtpVerificationMail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'User account created',
                'updated' => 'User account updated',
                'deleted' => 'User account deleted',
                default => $eventName,
            });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'dpa_agreed_at'     => 'datetime',
            'password'          => 'hashed',
            'otp'               => 'string',
            'otp_expires_at'    => 'datetime',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Check if the user has a valid OTP.
     *
     * @param string $otpCode the OTP code to verify
     * @return bool
     */
    public function hasValidOtp(string $otpCode): bool
    {
        if (blank($this->otp) || blank($this->otp_expires_at) || $this->otp_expires_at->isPast()) {
            return false;
        }

        return Hash::check($otpCode, $this->otp);
    }

    /**
     * Determine whether an unexpired OTP has already been issued.
     */
    public function hasActiveOtp(): bool
    {
        return filled($this->otp)
            && filled($this->otp_expires_at)
            && $this->otp_expires_at->isFuture();
    }

    /**
     * Generate and set a new 6-digit OTP.
     *
     * @return string the generated OTP
     */
    public function generateOtp(): string
    {
        $otp = (string) random_int(100000, 999999);
        $this->otp = Hash::make($otp);
        $this->otp_expires_at = now()->addMinutes(config('auth.verification_otp.expire', 10));
        $this->save();

        return $otp;
    }

    /**
     * Send the email-verification notification as a six-digit OTP.
     *
     * This keeps the MustVerifyEmail contract while using the OTP-based flow.
     */
    public function sendEmailVerificationNotification(): void
    {
        $otp = $this->generateOtp();

        Mail::to($this->email)->send(new OtpVerificationMail($otp));
    }

    /**
     * Clear the OTP (after successful verification).
     *
     * @return void
     */
    public function clearOtp(): void
    {
        $this->otp = null;
        $this->otp_expires_at = null;
        $this->save();
    }
}

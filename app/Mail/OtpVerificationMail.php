<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable;

    /**
     * The OTP code to send.
     *
     * @var string
     */
    public $otp;

    /**
     * Create a new message instance.
     *
     * @param string $otp
     * @return void
     */
    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->subject('Alcatt Portal — 6-digit Verification Code')
                    ->view('emails.otp-verification')
                    ->with([
                        'otp' => $this->otp,
                    ]);
    }
}
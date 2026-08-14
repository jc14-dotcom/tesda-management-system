<?php

namespace Tests\Feature\Auth;

use App\Mail\OtpVerificationMail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_otp_email_uses_the_shared_branded_mail_layout(): void
    {
        $html = (new OtpVerificationMail('123456'))->render();

        $this->assertStringContainsString('cid:alcatt-logo', $html);
        $this->assertStringContainsString('#2B2D7E', $html);
        $this->assertStringContainsString('#F4B400', $html);
        $this->assertStringContainsString('Verification code', $html);
        $this->assertStringContainsString('123456', $html);
        $this->assertStringContainsString('>Hello,</p>', $html);
        $this->assertStringNotContainsString('&lt;p style=', $html);
    }

    public function test_email_can_be_verified_with_a_valid_otp(): void
    {
        $user = User::factory()->unverified()->withOtp('123456')->create();

        Event::fake();

        $response = $this->actingAs($user)->post(route('verification.otp.verify'), [
            'otp' => '123456',
        ]);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertNull($user->fresh()->otp);
        $this->assertNull($user->fresh()->otp_expires_at);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_email_is_not_verified_with_an_invalid_otp(): void
    {
        $user = User::factory()->unverified()->withOtp('123456')->create();

        $response = $this->actingAs($user)->from(route('verification.notice'))
            ->post(route('verification.otp.verify'), ['otp' => '654321']);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->assertNotNull($user->fresh()->otp);
        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHasErrors('otp');
    }

    public function test_expired_otp_does_not_verify_the_email(): void
    {
        $user = User::factory()->unverified()->withOtp('123456')->state([
            'otp_expires_at' => now()->subMinute(),
        ])->create();

        $response = $this->actingAs($user)->from(route('verification.notice'))
            ->post(route('verification.otp.verify'), ['otp' => '123456']);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHasErrors(['otp' => 'This verification code has expired. Please request a new code.']);
    }

    public function test_resending_an_otp_replaces_the_previous_code(): void
    {
        Mail::fake();

        $user = User::factory()->unverified()->withOtp('123456')->create();

        $response = $this->actingAs($user)->from(route('verification.notice'))
            ->post(route('verification.send'));

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('status', 'otp-sent');
        $this->assertFalse(Hash::check('123456', $user->fresh()->otp));
        Mail::assertSent(OtpVerificationMail::class, function (OtpVerificationMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_otp_verification_attempts_are_rate_limited(): void
    {
        $user = User::factory()->unverified()->withOtp('123456')->create();

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->actingAs($user)->from(route('verification.notice'))
                ->post(route('verification.otp.verify'), ['otp' => '654321'])
                ->assertRedirect(route('verification.notice'));
        }

        $response = $this->actingAs($user)->from(route('verification.notice'))
            ->post(route('verification.otp.verify'), ['otp' => '654321']);

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('rate_limit_error');
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_register_as_pending_without_an_otp(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'agree_to_dpa' => true,
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('account_pending', true);

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertNull($user->otp);
        $this->assertNull($user->otp_expires_at);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }
}

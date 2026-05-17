<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    // ─── Store ───────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_add_a_certificate(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('certificates.store'), [
            'certificate_name' => 'NC II in Electrical Installation',
            'certificate_type' => 'nc_ii',
            'expiration_date'  => now()->addYear()->format('Y-m-d'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('certificates', [
            'user_id'          => $user->id,
            'certificate_name' => 'NC II in Electrical Installation',
            'certificate_type' => 'nc_ii',
            'status'           => 'valid',
        ]);
    }

    public function test_expired_date_sets_status_to_expired(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('certificates.store'), [
            'certificate_name' => 'Old Certificate',
            'certificate_type' => 'other',
            'expiration_date'  => now()->subDay()->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'status'  => 'expired',
        ]);
    }

    public function test_expiring_soon_sets_status_to_expiring(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('certificates.store'), [
            'certificate_name' => 'Expiring Certificate',
            'certificate_type' => 'other',
            'expiration_date'  => now()->addDays(10)->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'status'  => 'expiring',
        ]);
    }

    public function test_certificate_store_requires_name_and_type(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('certificates.store'), []);

        $response->assertSessionHasErrors(['certificate_name', 'certificate_type']);
    }

    public function test_guest_cannot_add_certificate(): void
    {
        $response = $this->post(route('certificates.store'), [
            'certificate_name' => 'Test',
            'certificate_type' => 'nc_i',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_invalid_certificate_type_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('certificates.store'), [
            'certificate_name' => 'Test',
            'certificate_type' => 'not_a_valid_type',
        ]);

        $response->assertSessionHasErrors(['certificate_type']);
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function test_user_can_delete_own_certificate(): void
    {
        $user        = User::factory()->create();
        $certificate = Certificate::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('certificates.destroy', $certificate));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseMissing('certificates', ['id' => $certificate->id]);
    }

    public function test_user_cannot_delete_another_users_certificate(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $cert  = Certificate::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->delete(route('certificates.destroy', $cert));

        $response->assertForbidden();
        $this->assertDatabaseHas('certificates', ['id' => $cert->id]);
    }
}

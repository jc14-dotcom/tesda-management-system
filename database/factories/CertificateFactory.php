<?php

namespace Database\Factories;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'          => \App\Models\User::factory(),
            'certificate_name' => fake()->words(3, true),
            'certificate_type' => fake()->randomElement(array_keys(\App\Models\Certificate::TYPE_LABELS)),
            'status'           => 'valid',
            'verification_status' => 'pending',
        ];
    }
}

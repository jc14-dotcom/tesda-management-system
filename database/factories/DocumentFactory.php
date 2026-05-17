<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'       => \App\Models\User::factory(),
            'document_name' => fake()->words(2, true),
            'type'          => fake()->randomElement(['cv', 'certificate', 'other']),
            'path'          => 'documents/1/test.pdf',
            'original_name' => 'test.pdf',
            'mime_type'     => 'application/pdf',
            'size'          => 102400,
            'is_primary'    => false,
        ];
    }
}

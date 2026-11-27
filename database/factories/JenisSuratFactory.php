<?php

namespace Database\Factories;

use App\Models\JenisSurat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JenisSurat>
 */
class JenisSuratFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->bothify('JS??##'),
            'nama' => fake()->words(3, true),
            'deskripsi' => fake()->sentence(),
            'template_view' => 'surat.pdf',
            'aktif' => true,
            'field_tambahan' => [],
        ];
    }
}

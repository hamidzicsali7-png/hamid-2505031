<?php

namespace Database\Factories;

use App\Models\NomorSuratCounter;
use App\Models\JenisSurat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NomorSuratCounter>
 */
class NomorSuratCounterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenis_surat_id' => JenisSurat::factory(),
            'tahun' => now()->year,
            'urutan_terakhir' => fake()->numberBetween(0, 1000),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\SyaratSurat;
use App\Models\JenisSurat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SyaratSurat>
 */
class SyaratSuratFactory extends Factory
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
            'nama_dokumen' => fake()->words(3, true),
            'wajib' => true,
        ];
    }
}

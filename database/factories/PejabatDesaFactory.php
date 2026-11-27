<?php

namespace Database\Factories;

use App\Models\PejabatDesa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PejabatDesa>
 */
class PejabatDesaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'jabatan' => fake()->randomElement(['Kepala Desa', 'Sekretaris Desa', 'Operator Desa']),
            'nip' => fake()->optional()->numerify('##################'),
            'tanda_tangan_path' => null,
            'aktif' => true,
        ];
    }
}

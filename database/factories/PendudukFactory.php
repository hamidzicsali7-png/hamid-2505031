<?php

namespace Database\Factories;

use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penduduk>
 */
class PendudukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => fake()->unique()->numerify('################'),
            'no_kk' => fake()->numerify('################'),
            'nama' => fake()->name(),
            'tempat_lahir' => fake()->city(),
            'tgl_lahir' => fake()->dateTimeBetween('-80 years', '-17 years')->format('Y-m-d'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'agama' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']),
            'pekerjaan' => fake()->jobTitle(),
            'status_perkawinan' => fake()->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
            'kewarganegaraan' => 'WNI',
            'alamat' => fake()->streetAddress(),
            'rt' => fake()->numerify('###'),
            'rw' => fake()->numerify('###'),
            'dusun' => 'Dusun '.fake()->numberBetween(1, 5),
            'user_id' => null,
        ];
    }
}

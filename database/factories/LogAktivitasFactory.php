<?php

namespace Database\Factories;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LogAktivitas>
 */
class LogAktivitasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'aksi' => fake()->randomElement(['login', 'buat_pengajuan', 'verifikasi', 'persetujuan']),
            'keterangan' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
        ];
    }
}

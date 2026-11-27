<?php

namespace Database\Factories;

use App\Models\PengaturanDesa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengaturanDesa>
 */
class PengaturanDesaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_desa' => 'Desa '.fake()->city(),
            'kecamatan' => 'Kecamatan '.fake()->city(),
            'kabupaten' => fake()->city(),
            'provinsi' => fake()->state(),
            'alamat' => fake()->address(),
            'kode_pos' => fake()->postcode(),
            'telepon' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'logo_path' => null,
            'nama_kades' => fake()->name(),
            'format_nomor_surat' => '470/{urutan}/DS/{bulan_romawi}/{tahun}',
        ];
    }
}

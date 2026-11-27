<?php

namespace Database\Factories;

use App\Models\SuratTerbit;
use App\Models\PejabatDesa;
use App\Models\PengajuanSurat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SuratTerbit>
 */
class SuratTerbitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pengajuan_surat_id' => PengajuanSurat::factory(),
            'nomor_surat' => '470/'.fake()->unique()->numerify('####').'/SKD/'.now()->year,
            'tgl_terbit' => fake()->date(),
            'pejabat_id' => PejabatDesa::factory(),
            'file_pdf' => null,
            'kode_verifikasi' => fake()->unique()->uuid(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\DokumenPengajuan;
use App\Models\PengajuanSurat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DokumenPengajuan>
 */
class DokumenPengajuanFactory extends Factory
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
            'nama_dokumen' => fake()->randomElement(['KTP', 'Kartu Keluarga', 'Surat Pengantar RT']),
            'file_path' => 'dokumen/'.fake()->uuid().'.pdf',
        ];
    }
}

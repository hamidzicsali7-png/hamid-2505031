<?php

namespace Database\Factories;

use App\Enums\StatusPengajuan;
use App\Models\PengajuanSurat;
use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PengajuanSurat>
 */
class PengajuanSuratFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_pengajuan' => 'PG-'.Str::upper(Str::random(12)),
            'user_id' => User::factory(),
            'penduduk_id' => Penduduk::factory(),
            'jenis_surat_id' => JenisSurat::factory(),
            'keperluan' => fake()->sentence(),
            'data_tambahan' => [],
            'status' => StatusPengajuan::Diajukan,
            'catatan' => null,
            'diverifikasi_oleh' => null,
            'disetujui_oleh' => null,
            'tgl_pengajuan' => now(),
        ];
    }
}

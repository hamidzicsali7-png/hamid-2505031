<?php

namespace Database\Seeders;

use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Database\Seeder;

class PendudukSeeder extends Seeder
{
    public function run(): void
    {
        $warga = User::where('email', 'warga@sikades.test')->firstOrFail();

        for ($nomor = 1; $nomor <= 30; $nomor++) {
            $nik = $nomor === 1
                ? $warga->nik
                : '320101010101'.str_pad((string) $nomor, 4, '0', STR_PAD_LEFT);
            $data = Penduduk::factory()->raw([
                'nik' => $nik,
                'no_kk' => '320101010101'.str_pad((string) ceil($nomor / 4), 4, '0', STR_PAD_LEFT),
                'user_id' => $nomor === 1 ? $warga->id : null,
            ]);

            Penduduk::updateOrCreate(['nik' => $nik], $data);
        }
    }
}

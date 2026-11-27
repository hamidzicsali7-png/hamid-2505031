<?php

namespace Database\Seeders;

use App\Models\PejabatDesa;
use Illuminate\Database\Seeder;

class PejabatDesaSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['nama' => 'Bapak Kepala Desa', 'jabatan' => 'Kepala Desa', 'nip' => '197001011995011001'],
            ['nama' => 'Ibu Sekretaris Desa', 'jabatan' => 'Sekretaris Desa', 'nip' => '197505052000122001'],
            ['nama' => 'Petugas Operator', 'jabatan' => 'Operator Desa', 'nip' => null],
        ] as $pejabat) {
            PejabatDesa::updateOrCreate(
                ['jabatan' => $pejabat['jabatan']],
                [...$pejabat, 'tanda_tangan_path' => null, 'aktif' => true],
            );
        }
    }
}

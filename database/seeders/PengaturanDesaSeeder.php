<?php

namespace Database\Seeders;

use App\Models\PengaturanDesa;
use Illuminate\Database\Seeder;

class PengaturanDesaSeeder extends Seeder
{
    public function run(): void
    {
        $pengaturan = PengaturanDesa::query()->firstOrNew();
        $pengaturan->fill([
            'nama_desa' => 'Desa Makmur',
            'kecamatan' => 'Sukamaju',
            'kabupaten' => 'Kabupaten Sejahtera',
            'provinsi' => 'Jawa Barat',
            'alamat' => 'Jl. Raya Desa Makmur No. 1',
            'kode_pos' => '40123',
            'telepon' => '0221234567',
            'email' => 'kantor@desamakmur.test',
            'nama_kades' => 'Bapak Kepala Desa',
            'format_nomor_surat' => '470/{urutan}/DS/{bulan_romawi}/{tahun}',
        ]);
        $pengaturan->save();
    }
}

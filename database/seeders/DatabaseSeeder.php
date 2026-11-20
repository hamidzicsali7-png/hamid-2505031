<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RoleSeeder::class, UserSeeder::class]);

        foreach ([
            ['kode' => 'SKTM', 'nama' => 'Surat Keterangan Tidak Mampu', 'deskripsi' => 'Untuk keperluan bantuan dan bantuan sosial'],
            ['kode' => 'SKU', 'nama' => 'Surat Keterangan Usaha', 'deskripsi' => 'Untuk keterangan usaha warga'],
            ['kode' => 'SKD', 'nama' => 'Surat Keterangan Domisili', 'deskripsi' => 'Untuk keterangan tempat tinggal'],
        ] as $jenis) {
            JenisSurat::updateOrCreate(['kode' => $jenis['kode']], $jenis);
        }
    }
}

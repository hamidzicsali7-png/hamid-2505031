<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (['admin', 'kades', 'sekretaris', 'warga'] as $role) {
            \Spatie\Permission\Models\Role::findOrCreate($role);
        }

        \App\Models\JenisSurat::insert([
            ['kode'=>'SKTM','nama'=>'Surat Keterangan Tidak Mampu','deskripsi'=>'Untuk keperluan bantuan dan bantuan sosial','aktif'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['kode'=>'SKU','nama'=>'Surat Keterangan Usaha','deskripsi'=>'Untuk keterangan usaha warga','aktif'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['kode'=>'SKD','nama'=>'Surat Keterangan Domisili','deskripsi'=>'Untuk keterangan tempat tinggal','aktif'=>1,'created_at'=>now(),'updated_at'=>now()],
        ]);

        $akun = [
            ['Admin Desa','admin@sikades.test','admin'],
            ['Kepala Desa','kades@sikades.test','kades'],
            ['Sekretaris Desa','sekretaris@sikades.test','sekretaris'],
            ['Budi Warga','warga@sikades.test','warga'],
        ];
        foreach ($akun as [$nama, $email, $role]) {
            $user = User::factory()->create(['name'=>$nama,'email'=>$email,'password'=>'password']);
            $user->assignRole($role);
            if ($role === 'warga') {
                \App\Models\Penduduk::create(['user_id'=>$user->id,'nik'=>'3201010101010001','nama'=>'Budi Warga','tempat_lahir'=>'Desa Makmur','tanggal_lahir'=>'1995-05-10','jenis_kelamin'=>'L','alamat'=>'Dusun 1 RT 001 RW 001','no_hp'=>'081234567890']);
            }
        }
    }
}

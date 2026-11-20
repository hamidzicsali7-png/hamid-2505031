<?php

namespace Database\Seeders;

use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['nama' => 'Admin Desa', 'email' => 'admin@sikades.test', 'peran' => 'admin'],
            ['nama' => 'Kepala Desa', 'email' => 'kades@sikades.test', 'peran' => 'kades'],
            ['nama' => 'Sekretaris Desa', 'email' => 'sekretaris@sikades.test', 'peran' => 'sekretaris'],
            ['nama' => 'Budi Warga', 'email' => 'warga@sikades.test', 'peran' => 'warga'],
        ];

        foreach ($akun as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nama'],
                    'nik' => $data['peran'] === 'warga' ? '3201010101010001' : null,
                    'no_hp' => $data['peran'] === 'warga' ? '081234567890' : null,
                    'password' => 'password',
                ],
            );
            $user->syncRoles([$data['peran']]);

            if ($data['peran'] === 'warga') {
                Penduduk::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nik' => $user->nik,
                        'nama' => $user->name,
                        'tempat_lahir' => 'Desa Makmur',
                        'tanggal_lahir' => '1995-05-10',
                        'jenis_kelamin' => 'L',
                        'alamat' => 'Dusun 1 RT 001 RW 001',
                        'no_hp' => $user->no_hp,
                    ],
                );
            }
        }
    }
}

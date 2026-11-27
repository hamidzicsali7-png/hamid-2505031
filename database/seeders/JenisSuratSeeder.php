<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use App\Models\SyaratSurat;
use Illuminate\Database\Seeder;

class JenisSuratSeeder extends Seeder
{
    public function run(): void
    {
        $jenisSurat = [
            [
                'kode' => 'SKD',
                'nama' => 'Surat Keterangan Domisili',
                'deskripsi' => 'Keterangan tempat tinggal warga.',
                'template_view' => 'surat.templates.domisili',
                'field_tambahan' => [
                    ['name' => 'lama_tinggal', 'label' => 'Lama Tinggal', 'type' => 'text', 'required' => true],
                ],
                'syarat' => ['Fotokopi KTP', 'Fotokopi Kartu Keluarga', 'Surat Pengantar RT/RW'],
            ],
            [
                'kode' => 'SKTM',
                'nama' => 'Surat Keterangan Tidak Mampu',
                'deskripsi' => 'Keterangan kondisi ekonomi untuk keperluan bantuan.',
                'template_view' => 'surat.templates.sktm',
                'field_tambahan' => [
                    ['name' => 'penghasilan', 'label' => 'Penghasilan per Bulan', 'type' => 'number', 'required' => true],
                    ['name' => 'jumlah_tanggungan', 'label' => 'Jumlah Tanggungan', 'type' => 'number', 'required' => true],
                ],
                'syarat' => ['Fotokopi KTP', 'Fotokopi Kartu Keluarga', 'Surat Pengantar RT/RW'],
            ],
            [
                'kode' => 'SKU',
                'nama' => 'Surat Keterangan Usaha',
                'deskripsi' => 'Keterangan kepemilikan atau kegiatan usaha warga.',
                'template_view' => 'surat.templates.usaha',
                'field_tambahan' => [
                    ['name' => 'nama_usaha', 'label' => 'Nama Usaha', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat_usaha', 'label' => 'Alamat Usaha', 'type' => 'textarea', 'required' => true],
                ],
                'syarat' => ['Fotokopi KTP', 'Fotokopi Kartu Keluarga', 'Surat Pengantar RT/RW'],
            ],
            [
                'kode' => 'SKL',
                'nama' => 'Surat Keterangan Kelahiran',
                'deskripsi' => 'Keterangan peristiwa kelahiran di wilayah desa.',
                'template_view' => 'surat.templates.kelahiran',
                'field_tambahan' => [
                    ['name' => 'nama_bayi', 'label' => 'Nama Bayi', 'type' => 'text', 'required' => true],
                    ['name' => 'tanggal_lahir_bayi', 'label' => 'Tanggal Lahir', 'type' => 'date', 'required' => true],
                ],
                'syarat' => ['Surat Keterangan Lahir', 'Fotokopi KTP Orang Tua', 'Fotokopi Kartu Keluarga'],
            ],
            [
                'kode' => 'SKM',
                'nama' => 'Surat Keterangan Kematian',
                'deskripsi' => 'Keterangan peristiwa kematian warga.',
                'template_view' => 'surat.templates.kematian',
                'field_tambahan' => [
                    ['name' => 'nama_almarhum', 'label' => 'Nama Almarhum', 'type' => 'text', 'required' => true],
                    ['name' => 'tanggal_meninggal', 'label' => 'Tanggal Meninggal', 'type' => 'date', 'required' => true],
                ],
                'syarat' => ['Surat Keterangan Kematian', 'Fotokopi KTP Almarhum', 'Fotokopi Kartu Keluarga'],
            ],
            [
                'kode' => 'SKCK',
                'nama' => 'Surat Pengantar SKCK',
                'deskripsi' => 'Surat pengantar untuk pengurusan SKCK.',
                'template_view' => 'surat.templates.pengantar',
                'field_tambahan' => [
                    ['name' => 'keperluan_skck', 'label' => 'Keperluan SKCK', 'type' => 'text', 'required' => true],
                ],
                'syarat' => ['Fotokopi KTP', 'Fotokopi Kartu Keluarga', 'Fotokopi Akta Kelahiran'],
            ],
        ];

        foreach ($jenisSurat as $data) {
            $syarat = $data['syarat'];
            unset($data['syarat']);
            $data['aktif'] = true;

            $jenis = JenisSurat::updateOrCreate(['kode' => $data['kode']], $data);

            foreach ($syarat as $namaDokumen) {
                SyaratSurat::updateOrCreate(
                    ['jenis_surat_id' => $jenis->id, 'nama_dokumen' => $namaDokumen],
                    ['wajib' => true],
                );
            }
        }
    }
}

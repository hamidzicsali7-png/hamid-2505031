<?php

namespace Tests\Feature;

use App\Models\JenisSurat;
use App\Models\PejabatDesa;
use App\Models\PengaturanDesa;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_manage_letter_types_officials_and_village_settings(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $this->get(route('admin.penduduk.create'))->assertOk()->assertSee('Tambah Penduduk');
        $this->get(route('admin.jenis-surat.create'))->assertOk()->assertSee('Syarat Dokumen');
        $this->get(route('admin.pejabat-desa.create'))->assertOk()->assertSee('Gambar Tanda Tangan');

        $this->post(route('admin.jenis-surat.store'), [
            'kode' => 'SKD-TEST',
            'nama' => 'Surat Domisili Test',
            'deskripsi' => 'Surat untuk uji CRUD',
            'aktif' => '1',
            'syarat' => ['Fotokopi KTP'],
            'field_tambahan' => [
                ['name' => 'lama_tinggal', 'label' => 'Lama Tinggal', 'type' => 'text', 'required' => '1'],
            ],
        ])->assertRedirect(route('admin.jenis-surat.index'));

        $jenisSurat = JenisSurat::where('kode', 'SKD-TEST')->firstOrFail();
        $this->assertDatabaseHas('syarat_surat', [
            'jenis_surat_id' => $jenisSurat->id,
            'nama_dokumen' => 'Fotokopi KTP',
            'wajib' => true,
        ]);
        $this->assertSame('lama_tinggal', $jenisSurat->field_tambahan[0]['name']);
        $this->get(route('admin.jenis-surat.show', $jenisSurat))->assertOk()->assertSee('Surat Domisili Test');

        $this->put(route('admin.jenis-surat.update', $jenisSurat), [
            'kode' => 'SKD-TEST',
            'nama' => 'Surat Domisili Diperbarui',
            'aktif' => '0',
            'syarat' => ['Surat Pengantar RT'],
            'field_tambahan' => [],
        ])->assertRedirect(route('admin.jenis-surat.show', $jenisSurat));

        $this->assertDatabaseHas('jenis_surat', ['id' => $jenisSurat->id, 'aktif' => false]);
        $this->assertDatabaseHas('syarat_surat', ['jenis_surat_id' => $jenisSurat->id, 'nama_dokumen' => 'Surat Pengantar RT']);

        $this->post(route('admin.pejabat-desa.store'), [
            'nama' => 'Rina Puspita',
            'jabatan' => 'Sekretaris Desa',
            'nip' => '198201012010012001',
            'aktif' => '1',
            'tanda_tangan' => UploadedFile::fake()->image('ttd.png'),
        ])->assertRedirect(route('admin.pejabat-desa.index'));

        $pejabat = PejabatDesa::where('nama', 'Rina Puspita')->firstOrFail();
        Storage::disk('public')->assertExists($pejabat->tanda_tangan_path);
        $this->get(route('admin.pejabat-desa.show', $pejabat))->assertOk()->assertSee('Sekretaris Desa');

        $this->seed(DatabaseSeeder::class);
        $pengaturan = PengaturanDesa::firstOrFail();
        $this->put(route('admin.pengaturan-desa.update'), [
            'nama_desa' => 'Desa Uji SIKADES',
            'kecamatan' => 'Cibiru',
            'kabupaten' => 'Bandung',
            'provinsi' => 'Jawa Barat',
            'alamat' => 'Jalan Desa No. 1',
            'kode_pos' => '40614',
            'telepon' => '022123456',
            'email' => 'desa@sikades.test',
            'nama_kades' => 'Dadan Rahmat',
            'format_nomor_surat' => '{nomor}/{kode}/{tahun}',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ])->assertRedirect(route('admin.pengaturan-desa.index'));

        $pengaturan->refresh();
        $this->assertSame('Desa Uji SIKADES', $pengaturan->nama_desa);
        Storage::disk('public')->assertExists($pengaturan->logo_path);
        $this->get(route('admin.pengaturan-desa.index'))->assertOk()->assertSee('Desa Uji SIKADES');
    }

    public function test_upload_tanda_tangan_rejects_image_extensions_with_non_image_content(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $filePath = tempnam(sys_get_temp_dir(), 'sikades-upload');
        file_put_contents($filePath, 'bukan gambar');

        try {
            $this->actingAs($admin)
                ->post(route('admin.pejabat-desa.store'), [
                    'nama' => 'Pejabat Uji',
                    'jabatan' => 'Sekretaris Desa',
                    'aktif' => '1',
                    'tanda_tangan' => new UploadedFile($filePath, 'palsu.png', null, UPLOAD_ERR_OK, true),
                ])
                ->assertSessionHasErrors('tanda_tangan');
        } finally {
            unlink($filePath);
        }

        $this->assertDatabaseMissing('pejabat_desa', ['nama' => 'Pejabat Uji']);
    }

    public function test_read_only_staff_can_view_master_data_but_cannot_change_it(): void
    {
        $this->seed(DatabaseSeeder::class);
        $sekretaris = User::where('email', 'sekretaris@sikades.test')->firstOrFail();

        $this->actingAs($sekretaris)
            ->get(route('admin.jenis-surat.index'))
            ->assertOk();

        $this->get(route('admin.pejabat-desa.index'))->assertOk();
        $this->get(route('admin.pengaturan-desa.index'))->assertOk();
        $this->get(route('admin.penduduk.index'))->assertOk();
        $this->get(route('admin.jenis-surat.create'))->assertForbidden();
        $this->put(route('admin.pengaturan-desa.update'), [])->assertForbidden();
    }

    public function test_admin_can_import_a_csv_template_and_create_residents(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.penduduk.template'))
            ->assertOk()
            ->assertDownload('template-impor-penduduk.xlsx');

        $csv = implode(',', [
            'nik', 'no_kk', 'nama', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin',
            'agama', 'pekerjaan', 'status_perkawinan', 'kewarganegaraan',
            'alamat', 'rt', 'rw', 'dusun',
        ])."\n".
            '3201010101010021,3201010101010001,Budi Santoso,Bandung,1992-03-04,L,Islam,Guru,Belum Kawin,WNI,Jalan Mawar,001,002,Dusun 1';

        $this->post(route('admin.penduduk.import'), [
            'file' => UploadedFile::fake()->createWithContent('penduduk.csv', $csv),
        ])->assertRedirect(route('admin.penduduk.index'))
            ->assertSessionHas('success', '1 data penduduk berhasil diimpor.');

        $this->assertDatabaseHas('penduduk', [
            'nik' => '3201010101010021',
            'nama' => 'Budi Santoso',
            'tgl_lahir' => '1992-03-04 00:00:00',
        ]);
    }

    public function test_invalid_csv_rows_are_reported_without_importing_partial_data(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $csv = "nik,no_kk,nama,tempat_lahir,tgl_lahir,jenis_kelamin,agama,pekerjaan,status_perkawinan,kewarganegaraan,alamat,rt,rw,dusun\n".
            '123,3201010101010001,Budi Santoso,Bandung,1992-03-04,L,Islam,Guru,Belum Kawin,WNI,Jalan Mawar,001,002,Dusun 1';

        $this->actingAs($admin)
            ->post(route('admin.penduduk.import'), [
                'file' => UploadedFile::fake()->createWithContent('penduduk.csv', $csv),
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('penduduk', ['nama' => 'Budi Santoso']);
    }
}

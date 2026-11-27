<?php

namespace Tests\Feature;

use App\Models\JenisSurat;
use App\Models\PejabatDesa;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\SuratTerbit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuratVerifikasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_kode_valid_menampilkan_keaslian_surat_dan_nik_yang_disamarkan(): void
    {
        $suratTerbit = $this->buatSuratTerbit();

        $this->get(route('surat.verifikasi', $suratTerbit->kode_verifikasi))
            ->assertOk()
            ->assertSee('Surat Asli dan Terdaftar')
            ->assertSee($suratTerbit->nomor_surat)
            ->assertSee('Surat Domisili')
            ->assertSee('Budi Santoso')
            ->assertSee('1471********0001')
            ->assertSee('Kepala Desa')
            ->assertDontSee('1471123456780001');
    }

    public function test_kode_tidak_valid_menampilkan_pesan_tidak_ditemukan(): void
    {
        $this->get(route('surat.verifikasi', 'kode-tidak-valid'))
            ->assertOk()
            ->assertSee('Surat Tidak Ditemukan');
    }

    public function test_pencarian_manual_menerima_kode_verifikasi_dan_nomor_surat(): void
    {
        $suratTerbit = $this->buatSuratTerbit();

        $this->get(route('surat.verifikasi.form', ['q' => $suratTerbit->kode_verifikasi]))
            ->assertOk()
            ->assertSee('Surat Asli dan Terdaftar');

        $this->get(route('surat.verifikasi.form', ['q' => $suratTerbit->nomor_surat]))
            ->assertOk()
            ->assertSee('Surat Asli dan Terdaftar');
    }

    public function test_url_verifikasi_menggunakan_base_url_aplikasi(): void
    {
        $this->assertSame(
            rtrim(config('app.url'), '/').'/verifikasi/kode-uji',
            route('surat.verifikasi', 'kode-uji'),
        );
    }

    private function buatSuratTerbit(): SuratTerbit
    {
        $penduduk = Penduduk::factory()->create([
            'nik' => '1471123456780001',
            'nama' => 'Budi Santoso',
        ]);
        $jenisSurat = JenisSurat::factory()->create(['nama' => 'Surat Domisili']);
        $pengajuan = PengajuanSurat::factory()->create([
            'penduduk_id' => $penduduk->id,
            'jenis_surat_id' => $jenisSurat->id,
        ]);
        $pejabat = PejabatDesa::factory()->create([
            'nama' => 'Siti Aminah',
            'jabatan' => 'Kepala Desa',
        ]);

        return SuratTerbit::factory()->create([
            'pengajuan_surat_id' => $pengajuan->id,
            'pejabat_id' => $pejabat->id,
            'nomor_surat' => '470/001/DS/I/2026',
            'kode_verifikasi' => 'kode-uji',
        ]);
    }
}

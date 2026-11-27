<?php

namespace Tests\Feature;

use App\Enums\StatusPengajuan;
use App\Models\JenisSurat;
use App\Models\PejabatDesa;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\PengaturanDesa;
use App\Models\SuratTerbit;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArsipLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_dapat_mencari_arsip_dan_mengunduh_pdf_surat(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $jenisSurat = JenisSurat::factory()->create(['nama' => 'Surat Domisili']);
        $pengajuan = $this->buatPengajuan($jenisSurat, StatusPengajuan::Selesai, now(), [
            'nama' => 'Budi Santoso',
            'nik' => '1471123456780001',
        ]);
        $suratTerbit = SuratTerbit::factory()->create([
            'pengajuan_surat_id' => $pengajuan->id,
            'tgl_terbit' => now()->toDateString(),
            'file_pdf' => 'surat-terbit/uji.pdf',
        ]);
        Storage::disk('local')->put($suratTerbit->file_pdf, '%PDF-uji');

        $this->actingAs($admin)
            ->get(route('admin.arsip-surat.index', [
                'q' => 'Budi',
                'jenis_surat_id' => $jenisSurat->id,
                'bulan' => now()->month,
                'tahun' => now()->year,
            ]))
            ->assertOk()
            ->assertSee($suratTerbit->nomor_surat)
            ->assertSee('Budi Santoso')
            ->assertSee('Surat Domisili')
            ->assertSee('Unduh PDF');

        $this->get(route('admin.pengajuan.pdf', [
            'pengajuan' => $pengajuan,
            'unduh' => 1,
        ]))->assertDownload('surat-'.$suratTerbit->id.'.pdf');
    }

    public function test_laporan_memfilter_periode_dan_jenis_dan_dapat_diekspor(): void
    {
        PengaturanDesa::factory()->create(['nama_desa' => 'Desa Uji']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $jenisSurat = JenisSurat::factory()->create(['nama' => 'Surat Domisili']);
        $jenisLain = JenisSurat::factory()->create(['nama' => 'Surat Usaha']);
        $this->buatPengajuan($jenisSurat, StatusPengajuan::Diajukan, now()->startOfMonth()->addDays(2));
        $this->buatPengajuan($jenisSurat, StatusPengajuan::Selesai, now()->startOfMonth()->addDays(4));
        $this->buatPengajuan($jenisLain, StatusPengajuan::Diajukan, now()->startOfMonth()->addDays(6));
        $filter = [
            'periode' => 'bulanan',
            'bulan' => now()->month,
            'tahun' => now()->year,
            'jenis_surat_id' => $jenisSurat->id,
        ];

        $response = $this->actingAs($admin)
            ->get(route('admin.laporan.index', $filter))
            ->assertOk()
            ->assertSee('Surat Domisili');

        $this->assertCount(1, $response->viewData('rekapJenis'));
        $this->assertSame(2, (int) $response->viewData('rekapJenis')->first()->jumlah);
        $this->assertSame(
            1,
            $response->viewData('rekapStatus')
                ->firstWhere('status', StatusPengajuan::Diajukan)
                ->jumlah,
        );

        $this->get(route('admin.laporan.excel', $filter))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $pdf = $this->get(route('admin.laporan.pdf', $filter))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
    }

    public function test_laporan_hanya_dapat_diakses_role_petugas(): void
    {
        $warga = User::factory()->create();
        $warga->assignRole('warga');

        $this->actingAs($warga)
            ->get(route('admin.laporan.index'))
            ->assertForbidden();

        foreach (['admin', 'kades', 'sekretaris'] as $role) {
            $petugas = User::factory()->create();
            $petugas->assignRole($role);

            $this->actingAs($petugas)
                ->get(route('admin.laporan.index'))
                ->assertOk();
        }
    }

    public function test_dashboard_menampilkan_grafik_dan_pengajuan_yang_lama_menunggu_verifikasi(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $jenisSurat = JenisSurat::factory()->create();
        $this->buatPengajuan($jenisSurat, StatusPengajuan::Diajukan, now()->subDays(3));
        $this->buatPengajuan($jenisSurat, StatusPengajuan::Diajukan, now()->subDay());
        $this->buatPengajuan($jenisSurat, StatusPengajuan::Diverifikasi, now()->subDays(4));

        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Surat Terbit per Bulan')
            ->assertSee('Pengajuan Masuk per Bulan')
            ->assertSee('Surat Terbit per Jenis')
            ->assertSee('Komposisi Surat Terbit')
            ->assertSee('Pengajuan Belum Diverifikasi');

        $this->assertSame(1, $response->viewData('menungguVerifikasiLama'));
    }

    /**
     * @param  array<string, string>  $pendudukData
     */
    private function buatPengajuan(
        JenisSurat $jenisSurat,
        StatusPengajuan $status,
        \Illuminate\Support\Carbon $tanggal,
        array $pendudukData = [],
    ): PengajuanSurat {
        $user = User::factory()->create();
        $penduduk = Penduduk::factory()->create($pendudukData);

        return PengajuanSurat::factory()->create([
            'user_id' => $user->id,
            'penduduk_id' => $penduduk->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => $status,
            'tgl_pengajuan' => $tanggal,
        ]);
    }
}

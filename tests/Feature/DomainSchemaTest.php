<?php

namespace Tests\Feature;

use App\Enums\StatusPengajuan;
use App\Models\DokumenPengajuan;
use App\Models\JenisSurat;
use App\Models\LogAktivitas;
use App\Models\NomorSuratCounter;
use App\Models\PejabatDesa;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\PengaturanDesa;
use App\Models\SuratTerbit;
use App\Models\SyaratSurat;
use App\Models\User;
use App\Services\AlurPengajuanService;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DomainSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_reference_data_and_thirty_residents(): void
    {
        $this->seed(DatabaseSeeder::class);

        $warga = User::where('email', 'warga@sikades.test')->firstOrFail();

        $this->assertDatabaseCount('jenis_surat', 6);
        $this->assertDatabaseCount('penduduk', 30);
        $this->assertDatabaseCount('pengaturan_desa', 1);
        $this->assertDatabaseHas('penduduk', [
            'nik' => $warga->nik,
            'user_id' => $warga->id,
        ]);
        $this->assertSame(6, JenisSurat::withCount('syaratSurat')->get()->count());
        $this->assertSame(18, SyaratSurat::count());
    }

    public function test_domain_models_cast_data_and_expose_both_sides_of_relations(): void
    {
        $user = User::factory()->create();
        $jenisSurat = JenisSurat::factory()->create([
            'field_tambahan' => [['name' => 'nama_usaha', 'type' => 'text']],
        ]);
        $penduduk = Penduduk::factory()->create(['user_id' => $user->id]);
        $syarat = SyaratSurat::factory()->create(['jenis_surat_id' => $jenisSurat->id, 'wajib' => true]);
        $pengajuan = PengajuanSurat::factory()->create([
            'user_id' => $user->id,
            'penduduk_id' => $penduduk->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => StatusPengajuan::Diajukan,
            'data_tambahan' => ['nama_usaha' => 'Warung Makmur'],
        ]);
        $dokumen = DokumenPengajuan::factory()->create(['pengajuan_surat_id' => $pengajuan->id]);
        $pejabat = PejabatDesa::factory()->create();
        $suratTerbit = SuratTerbit::factory()->create([
            'pengajuan_surat_id' => $pengajuan->id,
            'pejabat_id' => $pejabat->id,
        ]);
        $counter = NomorSuratCounter::factory()->create(['jenis_surat_id' => $jenisSurat->id]);
        $log = LogAktivitas::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(StatusPengajuan::class, $pengajuan->status);
        $this->assertSame(['nama_usaha' => 'Warung Makmur'], $pengajuan->data_tambahan);
        $this->assertIsArray($jenisSurat->field_tambahan);
        $this->assertIsBool($syarat->wajib);
        $this->assertIsBool($pejabat->aktif);
        $this->assertInstanceOf(Carbon::class, $penduduk->tgl_lahir);
        $this->assertInstanceOf(Carbon::class, $suratTerbit->tgl_terbit);

        $this->assertTrue($user->penduduk->is($penduduk));
        $this->assertTrue($penduduk->user->is($user));
        $this->assertTrue($jenisSurat->syaratSurat->contains($syarat));
        $this->assertTrue($syarat->jenisSurat->is($jenisSurat));
        $this->assertTrue($user->pengajuanSurat->contains($pengajuan));
        $this->assertTrue($penduduk->pengajuanSurat->contains($pengajuan));
        $this->assertTrue($jenisSurat->pengajuanSurat->contains($pengajuan));
        $this->assertTrue($pengajuan->user->is($user));
        $this->assertTrue($pengajuan->penduduk->is($penduduk));
        $this->assertTrue($pengajuan->jenisSurat->is($jenisSurat));
        $this->assertTrue($pengajuan->dokumenPengajuan->contains($dokumen));
        $this->assertTrue($dokumen->pengajuanSurat->is($pengajuan));
        $this->assertTrue($pengajuan->suratTerbit->is($suratTerbit));
        $this->assertTrue($suratTerbit->pengajuanSurat->is($pengajuan));
        $this->assertTrue($suratTerbit->pejabatDesa->is($pejabat));
        $this->assertTrue($pejabat->suratTerbit->contains($suratTerbit));
        $this->assertTrue($jenisSurat->nomorSuratCounter->contains($counter));
        $this->assertTrue($counter->jenisSurat->is($jenisSurat));
        $this->assertTrue($user->logAktivitas->contains($log));
        $this->assertTrue($log->user->is($user));
    }

    public function test_completing_an_application_issues_a_sequentially_numbered_letter(): void
    {
        $this->seed(RoleSeeder::class);
        $jenisSurat = JenisSurat::factory()->create(['kode' => 'SKD']);
        $pengajuan = PengajuanSurat::factory()->create([
            'jenis_surat_id' => $jenisSurat->id,
            'status' => StatusPengajuan::Disetujui,
        ]);
        PejabatDesa::factory()->create(['jabatan' => 'Kepala Desa', 'aktif' => true]);
        PengaturanDesa::factory()->create(['format_nomor_surat' => '{nomor}/{kode}/{tahun}']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        app(AlurPengajuanService::class)->transisikan(
            $pengajuan,
            StatusPengajuan::Selesai,
            $admin,
            null,
            '127.0.0.1',
        );

        $this->assertSame('0001/SKD/'.now()->year, $pengajuan->fresh()->suratTerbit->nomor_surat);
        $this->assertSame(StatusPengajuan::Selesai, $pengajuan->fresh()->status);
        $this->assertDatabaseHas('log_aktivitas', [
            'pengajuan_surat_id' => $pengajuan->id,
            'aksi' => 'pengajuan.status',
            'keterangan' => 'Status pengajuan '.$pengajuan->kode_pengajuan.' berubah dari Disetujui menjadi Selesai.',
        ]);
    }
}

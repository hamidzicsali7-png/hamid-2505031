<?php

namespace Tests\Feature;

use App\Enums\StatusPengajuan;
use App\Exceptions\TransisiPengajuanTidakSah;
use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\User;
use App\Services\AlurPengajuanService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlurPengajuanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_pengajuan_dapat_diverifikasi_operator_lalu_disetujui_kades_dan_mengirim_notifikasi(): void
    {
        [$warga, $pengajuan] = $this->buatPengajuan(StatusPengajuan::Diajukan);
        $sekretaris = User::factory()->create();
        $sekretaris->assignRole('sekretaris');
        $kades = User::factory()->create();
        $kades->assignRole('kades');

        $this->actingAs($sekretaris)
            ->patch(route('admin.pengajuan.status', $pengajuan), [
                'status' => StatusPengajuan::Diverifikasi->value,
            ])
            ->assertRedirect();

        $this->assertSame(StatusPengajuan::Diverifikasi, $pengajuan->fresh()->status);

        $this->actingAs($kades)
            ->patch(route('admin.pengajuan.status', $pengajuan), [
                'status' => StatusPengajuan::Disetujui->value,
            ])
            ->assertRedirect();

        $this->assertSame(StatusPengajuan::Disetujui, $pengajuan->fresh()->status);
        $this->assertSame(2, $warga->notifications()->count());
        $this->assertTrue($warga->notifications->contains(
            fn ($notifikasi) => $notifikasi->data['status'] === 'disetujui',
        ));
        $this->assertSame(2, $pengajuan->logAktivitas()->count());
    }

    public function test_pengajuan_ditolak_tanpa_alasan_gagal_dan_status_tidak_berubah(): void
    {
        [$warga, $pengajuan] = $this->buatPengajuan(StatusPengajuan::Diajukan);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->patch(route('admin.pengajuan.status', $pengajuan), [
                'status' => StatusPengajuan::Ditolak->value,
            ])
            ->assertSessionHasErrors('catatan');

        $this->assertSame(StatusPengajuan::Diajukan, $pengajuan->fresh()->status);
        $this->assertCount(0, $warga->notifications);
        $this->assertSame(0, $pengajuan->logAktivitas()->count());
    }

    public function test_antrean_persetujuan_hanya_dapat_diakses_kades(): void
    {
        [, $pengajuan] = $this->buatPengajuan(StatusPengajuan::Diverifikasi);
        $pengajuan->load('penduduk');
        $kades = User::factory()->create();
        $kades->assignRole('kades');
        $sekretaris = User::factory()->create();
        $sekretaris->assignRole('sekretaris');

        $this->actingAs($kades)
            ->get(route('admin.antrean-persetujuan.index'))
            ->assertOk()
            ->assertSee($pengajuan->penduduk->nama)
            ->assertSee('Setujui');

        $this->actingAs($sekretaris)
            ->get(route('admin.antrean-persetujuan.index'))
            ->assertForbidden();
    }

    public function test_notifikasi_dapat_ditandai_dibaca_dan_membuka_pengajuan_milik_pengguna(): void
    {
        [$warga, $pengajuan] = $this->buatPengajuan(StatusPengajuan::Diajukan);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        app(AlurPengajuanService::class)->transisikan(
            $pengajuan,
            StatusPengajuan::Diverifikasi,
            $admin,
        );
        $notifikasi = $warga->unreadNotifications()->firstOrFail();

        $this->actingAs($warga)
            ->post(route('notifikasi.baca', $notifikasi->id))
            ->assertRedirect(route('warga.pengajuan.show', $pengajuan));

        $this->assertNotNull($notifikasi->fresh()->read_at);
    }

    public function test_admin_dapat_memfilter_daftar_dan_melihat_detail_pengajuan(): void
    {
        [, $pengajuan] = $this->buatPengajuan(StatusPengajuan::Diajukan);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.pengajuan.index', ['q' => $pengajuan->kode_pengajuan, 'status' => 'diajukan']))
            ->assertOk()
            ->assertSee($pengajuan->kode_pengajuan);

        $this->get(route('admin.pengajuan.show', $pengajuan))
            ->assertOk()
            ->assertSee($pengajuan->kode_pengajuan)
            ->assertSee('Riwayat Status')
            ->assertSee('Tandai sudah diverifikasi');
    }

    public function test_transisi_status_yang_tidak_diizinkan_melempar_exception(): void
    {
        [, $pengajuan] = $this->buatPengajuan(StatusPengajuan::Diajukan);
        $kades = User::factory()->create();
        $kades->assignRole('kades');

        $this->expectException(TransisiPengajuanTidakSah::class);

        app(AlurPengajuanService::class)->transisikan(
            $pengajuan,
            StatusPengajuan::Diverifikasi,
            $kades,
        );
    }

    /**
     * @return array{0: User, 1: PengajuanSurat}
     */
    private function buatPengajuan(StatusPengajuan $status): array
    {
        $warga = User::factory()->create();
        $warga->assignRole('warga');
        $penduduk = Penduduk::factory()->create(['user_id' => $warga->id]);
        $jenisSurat = JenisSurat::factory()->create();
        $pengajuan = PengajuanSurat::factory()->create([
            'user_id' => $warga->id,
            'penduduk_id' => $penduduk->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => $status,
        ]);

        return [$warga, $pengajuan];
    }
}

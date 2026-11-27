<?php

namespace Tests\Feature;

use App\Enums\StatusPengajuan;
use App\Models\DokumenPengajuan;
use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\SyaratSurat;
use App\Models\User;
use App\Services\AlurPengajuanService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WargaPengajuanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_warga_can_only_view_their_own_applications(): void
    {
        Storage::fake('local');
        [$warga, $penduduk] = $this->buatWarga('3201010101010101');
        [$wargaLain, $pendudukLain] = $this->buatWarga('3201010101010102');
        $jenisSurat = JenisSurat::factory()->create();
        $milikWarga = PengajuanSurat::factory()->create([
            'user_id' => $warga->id,
            'penduduk_id' => $penduduk->id,
            'jenis_surat_id' => $jenisSurat->id,
        ]);
        $milikWargaLain = PengajuanSurat::factory()->create([
            'user_id' => $pendudukLain->user_id,
            'penduduk_id' => $pendudukLain->id,
            'jenis_surat_id' => $jenisSurat->id,
        ]);
        $dokumenLain = DokumenPengajuan::factory()->create([
            'pengajuan_surat_id' => $milikWargaLain->id,
            'file_path' => 'pengajuan/'.$milikWargaLain->id.'/rahasia.pdf',
        ]);
        Storage::disk('local')->put($dokumenLain->file_path, 'dokumen privat');

        $this->actingAs($warga)
            ->get(route('warga.pengajuan.show', $milikWargaLain))
            ->assertForbidden();

        $this->patch(route('warga.pengajuan.update', $milikWargaLain), [
            'jenis_surat_id' => $jenisSurat->id,
            'keperluan' => 'Akses tidak sah',
            'aksi' => 'draft',
        ])->assertForbidden();

        $this->actingAs($wargaLain)
            ->get(route('warga.pengajuan.dokumen', [$milikWarga, $dokumenLain]))
            ->assertForbidden();

        $this->actingAs($warga)
            ->get(route('warga.pengajuan.dokumen', [$milikWargaLain, $dokumenLain]))
            ->assertForbidden();

        $this->get(route('warga.pengajuan.index'))
            ->assertOk()
            ->assertSee($milikWarga->kode_pengajuan)
            ->assertDontSee($milikWargaLain->kode_pengajuan);
    }

    public function test_warga_can_save_a_draft_submit_it_and_resubmit_after_revision(): void
    {
        Storage::fake('local');
        [$warga, $penduduk] = $this->buatWarga('3201010101010201');
        $jenisSurat = JenisSurat::factory()->create([
            'field_tambahan' => [
                ['name' => 'nama_usaha', 'label' => 'Nama Usaha', 'type' => 'text', 'required' => true],
            ],
        ]);
        $syarat = SyaratSurat::factory()->create([
            'jenis_surat_id' => $jenisSurat->id,
            'nama_dokumen' => 'Fotokopi KTP',
            'wajib' => true,
        ]);

        $this->actingAs($warga)
            ->get(route('warga.pengajuan.create', ['jenis_surat_id' => $jenisSurat->id]))
            ->assertOk()
            ->assertSee('Data Pemohon')
            ->assertSee('Nama Usaha');

        $this->post(route('warga.pengajuan.store'), [
            'jenis_surat_id' => $jenisSurat->id,
            'keperluan' => 'Persyaratan administrasi',
            'aksi' => 'draft',
        ])->assertRedirect();

        $pengajuan = PengajuanSurat::firstOrFail();
        $this->assertSame(StatusPengajuan::Draft, $pengajuan->status);
        $this->assertMatchesRegularExpression('/^PGJ-\d{8}-0001$/', $pengajuan->kode_pengajuan);
        $this->assertDatabaseHas('log_aktivitas', [
            'pengajuan_surat_id' => $pengajuan->id,
            'aksi' => 'pengajuan.dibuat',
        ]);

        $this->patch(route('warga.pengajuan.update', $pengajuan), [
            'jenis_surat_id' => $jenisSurat->id,
            'keperluan' => 'Persyaratan administrasi',
            'aksi' => 'ajukan',
        ])->assertSessionHasErrors([
            'data_tambahan.nama_usaha',
            'dokumen.'.$syarat->id,
        ]);

        $this->patch(route('warga.pengajuan.update', $pengajuan), [
            'jenis_surat_id' => $jenisSurat->id,
            'keperluan' => 'Persyaratan administrasi',
            'aksi' => 'ajukan',
            'data_tambahan' => ['nama_usaha' => 'Warung Melati'],
            'dokumen' => [$syarat->id => UploadedFile::fake()->create('ktp.txt', 10, 'text/plain')],
        ])->assertSessionHasErrors('dokumen.'.$syarat->id);

        $this->patch(route('warga.pengajuan.update', $pengajuan), [
            'jenis_surat_id' => $jenisSurat->id,
            'keperluan' => 'Persyaratan administrasi',
            'aksi' => 'ajukan',
            'data_tambahan' => ['nama_usaha' => 'Warung Melati'],
            'dokumen' => [$syarat->id => UploadedFile::fake()->create('ktp.pdf', 2049, 'application/pdf')],
        ])->assertSessionHasErrors('dokumen.'.$syarat->id);

        $this->patch(route('warga.pengajuan.update', $pengajuan), [
            'jenis_surat_id' => $jenisSurat->id,
            'keperluan' => 'Persyaratan administrasi',
            'aksi' => 'ajukan',
            'data_tambahan' => ['nama_usaha' => 'Warung Melati'],
            'dokumen' => [$syarat->id => UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf')],
        ])->assertRedirect(route('warga.pengajuan.show', $pengajuan));

        $pengajuan->refresh();
        $dokumen = $pengajuan->dokumenPengajuan()->firstOrFail();
        $this->assertSame(StatusPengajuan::Diajukan, $pengajuan->status);
        $this->assertSame('Warung Melati', $pengajuan->data_tambahan['nama_usaha']);
        Storage::disk('local')->assertExists($dokumen->file_path);
        $this->assertSame(2, $pengajuan->logAktivitas()->count());
        $this->get(route('warga.pengajuan.dokumen', [$pengajuan, $dokumen]))->assertOk();

        $sekretaris = User::factory()->create();
        $sekretaris->assignRole('sekretaris');

        app(AlurPengajuanService::class)->transisikan(
            $pengajuan,
            StatusPengajuan::Revisi,
            $sekretaris,
            'Foto identitas kurang jelas.',
            '127.0.0.2',
        );

        $this->get(route('warga.pengajuan.show', $pengajuan))
            ->assertOk()
            ->assertSee('Foto identitas kurang jelas.')
            ->assertSee('Perbaiki dan Ajukan Ulang');

        $this->patch(route('warga.pengajuan.update', $pengajuan), [
            'jenis_surat_id' => $jenisSurat->id,
            'keperluan' => 'Persyaratan administrasi',
            'aksi' => 'ajukan',
            'data_tambahan' => ['nama_usaha' => 'Warung Melati'],
        ])->assertRedirect(route('warga.pengajuan.show', $pengajuan));

        $this->assertSame(StatusPengajuan::Diajukan, $pengajuan->fresh()->status);
        $this->assertSame(4, $pengajuan->logAktivitas()->count());
    }

    public function test_login_links_matching_nik_and_shows_guidance_when_no_match_exists(): void
    {
        $nikCocok = '3201010101010301';
        $penduduk = Penduduk::factory()->create(['nik' => $nikCocok, 'user_id' => null]);
        $wargaCocok = User::factory()->create(['nik' => $nikCocok]);
        $wargaCocok->assignRole('warga');

        $this->post('/login', ['email' => $wargaCocok->email, 'password' => 'password'])
            ->assertRedirect(route('warga.dashboard', absolute: false));
        $this->assertDatabaseHas('penduduk', [
            'id' => $penduduk->id,
            'user_id' => $wargaCocok->id,
        ]);

        auth()->logout();
        $wargaTanpaData = User::factory()->create(['nik' => '3201010101010302']);
        $wargaTanpaData->assignRole('warga');

        $this->post('/login', ['email' => $wargaTanpaData->email, 'password' => 'password'])
            ->assertRedirect(route('warga.dashboard', absolute: false))
            ->assertSessionHas('error');

        $this->get(route('warga.dashboard'))
            ->assertOk()
            ->assertSee('Data penduduk belum terhubung')
            ->assertSee('hubungi kantor desa');
    }

    public function test_dashboard_displays_real_application_counts_and_latest_application(): void
    {
        [$warga, $penduduk] = $this->buatWarga('3201010101010401');
        $jenisSurat = JenisSurat::factory()->create();
        $pengajuan = PengajuanSurat::factory()->create([
            'user_id' => $warga->id,
            'penduduk_id' => $penduduk->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => StatusPengajuan::Diajukan,
            'kode_pengajuan' => 'PGJ-20261127-0001',
        ]);

        $this->actingAs($warga)
            ->get(route('warga.dashboard'))
            ->assertOk()
            ->assertSee($jenisSurat->nama)
            ->assertSee($pengajuan->kode_pengajuan)
            ->assertSee('Diajukan')
            ->assertSee('>1<', false)
            ->assertDontSee('18 November 2026');
    }

    private function buatWarga(string $nik): array
    {
        $user = User::factory()->create(['nik' => $nik]);
        $user->assignRole('warga');
        $penduduk = Penduduk::factory()->create([
            'nik' => $nik,
            'user_id' => $user->id,
        ]);

        return [$user, $penduduk];
    }
}

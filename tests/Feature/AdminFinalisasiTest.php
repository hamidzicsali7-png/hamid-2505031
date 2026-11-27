<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFinalisasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_dapat_membuat_akun_petugas_dan_mengubah_perannya(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.pengguna.store'), [
                'name' => 'Petugas Baru',
                'nik' => '3201010101010101',
                'email' => 'petugas@sikades.test',
                'no_hp' => '081234567890',
                'role' => 'sekretaris',
                'password' => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
            ])
            ->assertRedirect(route('admin.pengguna.index'));

        $petugas = User::query()->where('email', 'petugas@sikades.test')->firstOrFail();
        $this->assertTrue($petugas->hasRole('sekretaris'));
        $this->assertTrue(password_verify('password-baru-123', $petugas->password));

        $this->put(route('admin.pengguna.update', $petugas), [
            'name' => 'Petugas Diperbarui',
            'nik' => $petugas->nik,
            'email' => $petugas->email,
            'no_hp' => $petugas->no_hp,
            'role' => 'kades',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.pengguna.index'));

        $this->assertSame('Petugas Diperbarui', $petugas->fresh()->name);
        $this->assertTrue($petugas->fresh()->hasRole('kades'));
    }

    public function test_kelola_pengguna_hanya_admin_dan_tidak_dapat_mengangkat_warga_dari_url(): void
    {
        $sekretaris = User::factory()->create();
        $sekretaris->assignRole('sekretaris');
        $warga = User::factory()->create();
        $warga->assignRole('warga');

        $this->actingAs($sekretaris)
            ->get(route('admin.pengguna.index'))
            ->assertForbidden();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->put(route('admin.pengguna.update', $warga), [
                'name' => $warga->name,
                'nik' => $warga->nik,
                'email' => $warga->email,
                'role' => 'admin',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertForbidden();

        $this->assertTrue($warga->fresh()->hasRole('warga'));
    }

    public function test_admin_terakhir_tidak_dapat_mengubah_perannya_sendiri(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->put(route('admin.pengguna.update', $admin), [
                'name' => $admin->name,
                'nik' => $admin->nik,
                'email' => $admin->email,
                'no_hp' => $admin->no_hp,
                'role' => 'kades',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    public function test_log_aktivitas_dapat_difilter_dan_dipaginasi_hanya_oleh_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();

        foreach (range(1, 21) as $nomor) {
            LogAktivitas::create([
                'user_id' => $user->id,
                'aksi' => $nomor === 1 ? 'pengajuan.status' : 'pengajuan.dibuat',
                'keterangan' => 'Aktivitas '.$nomor,
                'ip_address' => '203.0.113.10',
            ]);
        }

        $response = $this->actingAs($admin)
            ->get(route('admin.log-aktivitas.index', ['aksi' => 'pengajuan.status']))
            ->assertOk()
            ->assertSee('Aktivitas 1')
            ->assertDontSee('Aktivitas 2')
            ->assertDontSee('203.0.113.10');

        $this->assertSame(1, $response->viewData('logAktivitas')->total());
        $this->assertTrue($response->viewData('logAktivitas')->hasPages() === false);

        LogAktivitas::query()->update(['aksi' => 'pengajuan.dibuat']);
        $response = $this->get(route('admin.log-aktivitas.index'))
            ->assertOk()
            ->assertSee('Aktivitas 1');
        $this->assertSame(21, $response->viewData('logAktivitas')->total());
        $this->assertCount(20, $response->viewData('logAktivitas'));

        $warga = User::factory()->create();
        $warga->assignRole('warga');
        $this->actingAs($warga)
            ->get(route('admin.log-aktivitas.index'))
            ->assertForbidden();
    }
}

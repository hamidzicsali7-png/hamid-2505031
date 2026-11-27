<?php

namespace Tests\Feature;

use App\Models\Penduduk;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPendudukCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_create_update_search_and_delete_penduduk(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $data = $this->dataPenduduk('3201010101010011');

        $this->actingAs($admin)
            ->post(route('admin.penduduk.store'), $data)
            ->assertRedirect(route('admin.penduduk.index'))
            ->assertSessionHas('success');

        $penduduk = Penduduk::where('nik', $data['nik'])->firstOrFail();
        $this->assertDatabaseHas('penduduk', [
            'nik' => $data['nik'],
            'nama' => 'Siti Aminah',
        ]);

        $this->get(route('admin.penduduk.index', ['q' => '3201010101010011', 'rt' => '001']))
            ->assertOk()
            ->assertSee('Siti Aminah');

        $this->put(route('admin.penduduk.update', $penduduk), [...$data, 'nama' => 'Siti Aminah Putri'])
            ->assertRedirect(route('admin.penduduk.show', $penduduk));

        $this->assertDatabaseHas('penduduk', [
            'id' => $penduduk->id,
            'nama' => 'Siti Aminah Putri',
        ]);

        $this->delete(route('admin.penduduk.destroy', $penduduk))
            ->assertRedirect(route('admin.penduduk.index'));

        $this->assertDatabaseMissing('penduduk', ['id' => $penduduk->id]);
    }

    public function test_penduduk_nik_must_be_sixteen_unique_digits(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.penduduk.store'), $this->dataPenduduk('12345'))
            ->assertSessionHasErrors('nik');
    }

    public function test_kades_can_read_penduduk_but_cannot_mutate_it(): void
    {
        $kades = User::factory()->create();
        $kades->assignRole('kades');

        $this->actingAs($kades)
            ->get(route('admin.penduduk.index'))
            ->assertOk();

        $this->post(route('admin.penduduk.store'), $this->dataPenduduk('3201010101010012'))
            ->assertForbidden();
    }

    private function dataPenduduk(string $nik): array
    {
        return [
            'nik' => $nik,
            'no_kk' => '3201010101010001',
            'nama' => 'Siti Aminah',
            'tempat_lahir' => 'Bandung',
            'tgl_lahir' => '1990-05-10',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'pekerjaan' => 'Guru',
            'status_perkawinan' => 'Kawin',
            'kewarganegaraan' => 'WNI',
            'alamat' => 'Jalan Melati No. 1',
            'rt' => '001',
            'rw' => '002',
            'dusun' => 'Melati',
        ];
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_warga_cannot_access_admin_dashboard(): void
    {
        $warga = User::factory()->create();
        $warga->assignRole('warga');

        $this->actingAs($warga)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_warga_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('warga.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_warga_login_redirects_to_warga_dashboard(): void
    {
        $warga = User::factory()->create();
        $warga->assignRole('warga');

        $this->post('/login', ['email' => $warga->email, 'password' => 'password'])
            ->assertRedirect(route('warga.dashboard', absolute: false));
    }
}

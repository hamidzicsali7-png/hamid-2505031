<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_page_404_is_rendered_without_debug_details(): void
    {
        config(['app.debug' => false]);

        $this->get('/halaman-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman Tidak Ditemukan');
    }

    public function test_custom_page_403_is_rendered_for_unauthorized_role(): void
    {
        config(['app.debug' => false]);
        $this->seed(RoleSeeder::class);
        $warga = User::factory()->create();
        $warga->assignRole('warga');

        $this->actingAs($warga)
            ->get(route('admin.dashboard'))
            ->assertForbidden()
            ->assertSee('Akses Ditolak');
    }

    public function test_custom_page_500_does_not_expose_exception_details(): void
    {
        config(['app.debug' => false]);
        Route::get('/_uji-gangguan-server', function (): never {
            throw new RuntimeException('secret-internal-error');
        });

        $this->get('/_uji-gangguan-server')
            ->assertInternalServerError()
            ->assertSee('Terjadi Gangguan')
            ->assertDontSee('secret-internal-error');
    }
}

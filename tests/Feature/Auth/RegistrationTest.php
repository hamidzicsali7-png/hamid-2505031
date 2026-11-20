<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk()
            ->assertSee('Nama Lengkap')
            ->assertSee('NIK')
            ->assertSee('Nomor HP');
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Test User',
            'nik' => '3201010101010002',
            'email' => 'test@example.com',
            'no_hp' => '081234567891',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('warga.dashboard', absolute: false));

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertSame('3201010101010002', $user->nik);
        $this->assertSame('081234567891', $user->no_hp);
        $this->assertTrue($user->hasRole('warga'));
    }

    public function test_registration_fails_when_nik_is_already_registered(): void
    {
        User::factory()->create(['nik' => '3201010101010003']);

        $response = $this->from('/register')->post('/register', [
            'nama' => 'Test User',
            'nik' => '3201010101010003',
            'email' => 'test@example.com',
            'no_hp' => '081234567891',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register')->assertSessionHasErrors('nik');
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }
}

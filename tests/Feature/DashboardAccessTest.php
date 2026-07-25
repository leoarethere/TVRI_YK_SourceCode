<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_gets_403(): void
    {
        $user = User::factory()->create(['role' => 'pegawai']);
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'pegawai']);

        $routes = [
            route('dashboard.postingan.index'),
            route('dashboard.berita.index'),
            route('dashboard.penyiaran.index'),
            route('dashboard.users.index'),
            route('dashboard.visi-misi.index'),
            route('dashboard.sejarah.index'),
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(403);
        }
    }

    public function test_admin_can_access_all_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $routes = [
            route('dashboard.postingan.index'),
            route('dashboard.berita.index'),
            route('dashboard.users.index'),
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_displays(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('login'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->post(route('login'), []);
        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_employee_rejected_from_dashboard_portal_gets_a_fresh_session(): void
    {
        $employee = User::factory()->create([
            'role' => 'pegawai',
            'email' => 'pegawai@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->withSession([
            'url.intended' => '/dashboard/users',
            'sensitive-residual' => 'must-be-cleared',
        ])->post(route('login'), [
            'email' => $employee->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email')
            ->assertSessionMissing('url.intended')
            ->assertSessionMissing('sensitive-residual');

        $this->assertGuest();
    }

    public function test_dashboard_user_rejected_from_employee_portal_gets_a_fresh_session(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'root-portal@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->withSession([
            'url.intended' => '/pegawai/private',
            'sensitive-residual' => 'must-be-cleared',
        ])->post('/pegawai/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email')
            ->assertSessionMissing('url.intended')
            ->assertSessionMissing('sensitive-residual');

        $this->assertGuest();
    }
}

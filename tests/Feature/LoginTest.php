<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Muat gambar captcha untuk mengisi session jawaban dan kembalikan
     * jawabannya untuk dikirim bersama request berikutnya.
     */
    private function freshCaptcha(): string
    {
        $this->get('/captcha');

        return (string) $this->app['session']->get('captcha.answer');
    }

    public function test_login_page_displays(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    public function test_login_page_displays_captcha_field(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi captcha', false);
        $response->assertSee('name="captcha"', false);
    }

    public function test_captcha_image_endpoint_renders_svg_and_stores_answer(): void
    {
        $response = $this->get('/captcha');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');

        $answer = $this->app['session']->get('captcha.answer');
        $this->assertIsInt($answer);
        $this->assertGreaterThanOrEqual(2, $answer);
        $this->assertLessThanOrEqual(18, $answer);
    }

    public function test_login_without_captcha_is_rejected(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertGuest();
    }

    public function test_login_with_wrong_captcha_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
            'captcha' => random_int(2, 18),
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertGuest();
    }

    public function test_login_with_stale_captcha_cannot_be_replayed(): void
    {
        $answer = $this->freshCaptcha();
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Percobaan pertama sudah memakai jawaban ini (gagal karena password salah).
        $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
            'captcha' => $answer,
        ]);

        $this->assertGuest();

        // Jawaban yang sama tidak dapat dipakai dua kali.
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
            'captcha' => $answer,
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertGuest();
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
            'captcha' => $this->freshCaptcha(),
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
            'captcha' => $this->freshCaptcha(),
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
                'captcha' => $this->freshCaptcha(),
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
            'captcha' => $this->freshCaptcha(),
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
            'captcha' => $this->freshCaptcha(),
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
            'captcha' => $this->freshCaptcha(),
        ])->assertSessionHasErrors('email')
            ->assertSessionMissing('url.intended')
            ->assertSessionMissing('sensitive-residual');

        $this->assertGuest();
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use RuntimeException;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->setLocale('id');
    }

    public function test_forgot_password_page_displays_for_guests(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Lupa Kata Sandi?')
            ->assertSee('Kirim Tautan Reset Kata Sandi');
    }

    public function test_authenticated_user_cannot_access_password_reset_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('password.request'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_email_validation_message_is_in_indonesian(): void
    {
        $this->post(route('password.email'), ['email' => ' '])
            ->assertSessionHasErrors([
                'email' => 'Alamat email wajib diisi.',
            ]);
    }

    public function test_known_and_unknown_emails_receive_the_same_generic_response(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'known@example.test',
        ]);

        $known = $this->post(route('password.email'), [
            'email' => '  KNOWN@example.test  ',
        ]);
        $unknown = $this->post(route('password.email'), [
            'email' => 'unknown@example.test',
        ]);

        $message = __('passwords.request_received');

        $known->assertSessionHas('success', $message)->assertSessionDoesntHaveErrors();
        $unknown->assertSessionHas('success', $message)->assertSessionDoesntHaveErrors();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_mail_transport_failure_does_not_expose_an_internal_error(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andThrow(new RuntimeException('SMTP tidak tersedia'));

        $this->post(route('password.email'), [
            'email' => 'user@example.test',
        ])->assertSessionHas('success', __('passwords.request_received'));
    }

    public function test_forgot_password_requests_are_rate_limited_by_ip(): void
    {
        Notification::fake();

        foreach (range(1, 5) as $attempt) {
            $this->post(route('password.email'), [
                'email' => "unknown{$attempt}@example.test",
            ])->assertRedirect();
        }

        $this->post(route('password.email'), [
            'email' => 'blocked@example.test',
        ])->assertTooManyRequests();
    }

    public function test_reset_notification_is_localized_queued_and_encrypted(): void
    {
        $user = User::factory()->create([
            'name' => 'Pengguna Uji',
            'email' => 'user@example.test',
        ]);
        $notification = new ResetPasswordNotification('secure-token');
        $mail = $notification->toMail($user);

        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertInstanceOf(ShouldBeEncrypted::class, $notification);
        $this->assertSame('Atur Ulang Kata Sandi - TVRI D.I. Yogyakarta', $mail->subject);
        $this->assertSame('Atur Ulang Kata Sandi', $mail->actionText);
        $this->assertStringContainsString('reset-password/secure-token', $mail->actionUrl);
        $this->assertStringContainsString('email=user%40example.test', $mail->actionUrl);
    }

    public function test_user_can_reset_password_and_existing_database_sessions_are_revoked(): void
    {
        config(['session.driver' => 'database']);
        Event::fake([PasswordReset::class]);

        $user = User::factory()->create([
            'email' => 'reset@example.test',
            'password' => Hash::make('OldPassword123!'),
            'remember_token' => 'old-remember-token',
        ]);
        $token = Password::createToken($user);

        DB::table(config('session.table', 'sessions'))->insert([
            'id' => 'existing-user-session',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Password reset test',
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', __('passwords.reset'));

        $freshUser = $user->fresh();

        $this->assertTrue(Hash::check('NewPassword123!', $freshUser->password));
        $this->assertNotSame('old-remember-token', $freshUser->remember_token);
        $this->assertDatabaseMissing(config('session.table', 'sessions'), [
            'id' => 'existing-user-session',
        ]);
        Event::assertDispatched(PasswordReset::class, fn ($event) => $event->user->is($user));

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'AnotherPassword123!',
            'password_confirmation' => 'AnotherPassword123!',
        ])->assertSessionHasErrors([
            'email' => __('passwords.invalid'),
        ]);
    }

    public function test_weak_password_is_rejected(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('OldPassword123!', $user->fresh()->password));
    }

    public function test_expired_token_cannot_be_used(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->travel(61)->minutes();

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertSessionHasErrors([
            'email' => __('passwords.invalid'),
        ]);
    }

    public function test_employee_reset_flow_returns_to_employee_portal(): void
    {
        $user = User::factory()->create([
            'role' => 'pegawai',
            'email' => 'pegawai@example.test',
        ]);
        $token = Password::createToken($user);

        $this->get(route('pegawai.login'))
            ->assertSee(route('password.request', ['portal' => 'pegawai']), false);

        $this->get(route('password.request', ['portal' => 'pegawai']))
            ->assertSee('Kembali ke Portal Pegawai');

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect(route('pegawai.login'));

        $mail = (new ResetPasswordNotification('employee-token'))->toMail($user);
        $this->assertStringContainsString('portal=pegawai', $mail->actionUrl);
    }
}

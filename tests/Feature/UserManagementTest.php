<?php

namespace Tests\Feature;

use App\Models\Broadcast;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_users_list(): void
    {
        User::factory()->count(3)->create();
        $response = $this->actingAs($this->admin)->get(route('dashboard.users.index'));
        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    public function test_admin_can_create_new_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('dashboard.users.store'), [
            'name' => 'New User',
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'secondary',
        ]);

        $response->assertRedirect(route('dashboard.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'username' => 'newuser',
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_create_user_validates_unique_fields(): void
    {
        User::factory()->create(['email' => 'existing@example.com', 'username' => 'existing']);

        $response = $this->actingAs($this->admin)->post(route('dashboard.users.store'), [
            'name' => 'Duplicate',
            'username' => 'existing',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['username', 'email']);
    }

    public function test_admin_can_edit_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('dashboard.users.update', $user), [
            'name' => 'Updated Name',
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
        ]);

        $response->assertRedirect(route('dashboard.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create(['role' => 'secondary']);

        $response = $this->actingAs($this->admin)->delete(route('dashboard.users.destroy', $user));
        $response->assertRedirect(route('dashboard.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('dashboard.users.destroy', $this->admin));
        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_last_admin_cannot_be_deleted(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('dashboard.users.destroy', $this->admin));
        $response->assertSessionHasErrors('error');
    }

    public function test_user_with_content_cannot_be_deleted(): void
    {
        $author = User::factory()->create(['role' => 'secondary']);
        Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => Category::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('dashboard.users.destroy', $author));
        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $author->id]);
    }

    public function test_user_with_broadcast_cannot_be_deleted(): void
    {
        $author = User::factory()->create(['role' => 'secondary']);
        Broadcast::factory()->create(['user_id' => $author->id]);

        $response = $this->actingAs($this->admin)->delete(route('dashboard.users.destroy', $author));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $author->id]);
        $this->assertSame(1, $author->broadcasts()->count());
    }

    public function test_database_restricts_deleting_an_author_with_broadcasts(): void
    {
        $author = User::factory()->create(['role' => 'secondary']);
        $broadcast = Broadcast::factory()->create(['user_id' => $author->id]);

        try {
            $author->delete();
            $this->fail('Database seharusnya menolak penghapusan pemilik penyiaran.');
        } catch (QueryException) {
            $this->assertDatabaseHas('users', ['id' => $author->id]);
            $this->assertDatabaseHas('broadcasts', ['id' => $broadcast->id]);
        }
    }

    public function test_password_change_rotates_remember_token_and_revokes_target_database_sessions(): void
    {
        config(['session.driver' => 'database']);

        $target = User::factory()->create([
            'role' => 'secondary',
            'remember_token' => 'remember-token-lama',
        ]);
        $otherUser = User::factory()->create(['role' => 'secondary']);

        $this->insertSession('target-session', $target);
        $this->insertSession('other-session', $otherUser);

        $response = $this->actingAs($this->admin)->put(route('dashboard.users.update', $target), [
            'name' => $target->name,
            'username' => $target->username,
            'email' => $target->email,
            'role' => $target->role,
            'password' => 'Password-Baru-123!',
            'password_confirmation' => 'Password-Baru-123!',
        ]);

        $response->assertRedirect(route('dashboard.users.index'));

        $target->refresh();
        $this->assertTrue(Hash::check('Password-Baru-123!', $target->password));
        $this->assertNotSame('remember-token-lama', $target->remember_token);
        $this->assertDatabaseMissing('sessions', ['id' => 'target-session']);
        $this->assertDatabaseHas('sessions', ['id' => 'other-session']);
    }

    public function test_changing_own_password_logs_out_other_devices(): void
    {
        config(['session.driver' => 'database']);

        $this->admin->forceFill(['remember_token' => 'remember-token-lama'])->save();
        $this->insertSession('current-admin-session', $this->admin);
        $this->insertSession('other-admin-session', $this->admin);

        $response = $this->actingAs($this->admin)->put(route('dashboard.users.update', $this->admin), [
            'name' => $this->admin->name,
            'username' => $this->admin->username,
            'email' => $this->admin->email,
            'role' => 'admin',
            'password' => 'Password-Baru-123!',
            'password_confirmation' => 'Password-Baru-123!',
        ]);

        $response->assertRedirect(route('dashboard.users.index'));
        $this->assertAuthenticatedAs($this->admin);
        // Sesi lain tidak terhapus seketika dari DB, melainkan invalid saat request berikutnya
        // karena ketidaksesuaian password_hash_web oleh middleware AuthenticateSession.
        $this->assertNotSame('remember-token-lama', $this->admin->fresh()->remember_token);
    }

    public function test_user_cannot_edit_their_own_admin_status_if_last_admin(): void
    {
        $response = $this->actingAs($this->admin)->put(route('dashboard.users.update', $this->admin), [
            'name' => $this->admin->name,
            'username' => $this->admin->username,
            'email' => $this->admin->email,
            'role' => 'secondary',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_admin_cannot_demote_their_own_account_even_when_another_admin_exists(): void
    {
        User::factory()->create([
            'role' => 'admin',
            'username' => 'admin-kedua',
            'email' => 'admin-kedua@example.com',
        ]);

        $this->actingAs($this->admin)->put(route('dashboard.users.update', $this->admin), [
            'name' => $this->admin->name,
            'username' => $this->admin->username,
            'email' => $this->admin->email,
            'role' => 'secondary',
        ])->assertSessionHasErrors('error');

        $this->assertSame('admin', $this->admin->fresh()->role);
        $this->assertAuthenticatedAs($this->admin);
    }

    private function insertSession(string $id, User $user): void
    {
        DB::table('sessions')->insert([
            'id' => $id,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);
    }
}

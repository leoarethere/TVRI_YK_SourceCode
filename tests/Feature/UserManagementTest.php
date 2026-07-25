<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        \App\Models\Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => \App\Models\Category::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('dashboard.users.destroy', $author));
        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $author->id]);
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
}

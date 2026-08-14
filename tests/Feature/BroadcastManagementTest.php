<?php

namespace Tests\Feature;

use App\Models\Broadcast;
use App\Models\BroadcastCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class BroadcastManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_resource_binds_and_updates_the_requested_broadcast(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BroadcastCategory::factory()->create();
        $broadcast = Broadcast::factory()->create([
            'user_id' => $admin->id,
            'broadcast_category_id' => $category->id,
            'title' => 'Program Lama',
            'slug' => 'program-lama',
        ]);

        $response = $this->actingAs($admin)->put(
            route('dashboard.penyiaran.update', $broadcast),
            [
                'title' => 'Program Baru',
                'slug' => 'program-baru',
                'broadcast_category_id' => $category->id,
                'synopsis' => 'Sinopsis baru.',
                'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'action' => 'publish',
                'is_active' => '1',
            ],
        );

        $response->assertRedirect(route('dashboard.penyiaran.index'));
        $this->assertDatabaseHas('broadcasts', [
            'id' => $broadcast->id,
            'title' => 'Program Baru',
            'slug' => 'program-baru',
        ]);
        $this->assertDatabaseCount('broadcasts', 1);
    }

    public function test_failed_public_storage_write_does_not_create_a_broadcast(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BroadcastCategory::factory()->create();

        $disk = Mockery::mock();
        $disk->shouldReceive('put')->once()->andReturnFalse();
        Storage::shouldReceive('disk')->once()->with('public')->andReturn($disk);

        $response = $this->actingAs($admin)
            ->from(route('dashboard.penyiaran.create'))
            ->post(route('dashboard.penyiaran.store'), [
                'title' => 'Program Gagal Upload',
                'slug' => 'program-gagal-upload',
                'broadcast_category_id' => $category->id,
                'poster' => UploadedFile::fake()->image('poster.png', 640, 360),
                'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'synopsis' => 'Tidak boleh tersimpan tanpa poster.',
                'action' => 'publish',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('dashboard.penyiaran.create'));
        $response->assertSessionHasErrors('poster');
        $this->assertDatabaseMissing('broadcasts', [
            'slug' => 'program-gagal-upload',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Broadcast;
use App\Models\BroadcastCategory;
use App\Models\Category;
use App\Models\InfoMagang;
use App\Models\JadwalAcara;
use App\Models\JadwalCategory;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_post_with_user_and_category()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Tech', 'slug' => 'tech', 'color' => 'blue']);

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Post',
            'slug' => 'test-post',
            'body' => 'This is a test post.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Verifikasi DB Logic
        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);

        // Verifikasi Relasi (Unit Logic)
        $this->assertEquals($user->id, $post->user->id);
        $this->assertEquals('Tech', $post->category->name);
    }

    #[Test]
    public function it_can_create_news_with_user_and_news_category()
    {
        $user = User::factory()->create();
        $newsCategory = NewsCategory::create(['name' => 'Politik', 'slug' => 'politik']);

        $news = News::create([
            'user_id' => $user->id,
            'news_category_id' => $newsCategory->id,
            'title' => 'Berita Penting',
            'slug' => 'berita-penting',
            'body' => 'Isi berita penting.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Verifikasi
        $this->assertDatabaseHas('news', ['slug' => 'berita-penting']);
        $this->assertEquals('Politik', $news->newsCategory->name);
        $this->assertEquals($user->name, $news->user->name);
    }

    #[Test]
    public function it_can_create_broadcast_with_user_and_broadcast_category()
    {
        $user = User::factory()->create();
        $bcCategory = BroadcastCategory::create(['name' => 'Talkshow', 'slug' => 'talkshow', 'color' => 'red']);

        $broadcast = Broadcast::create([
            'user_id' => $user->id,
            'broadcast_category_id' => $bcCategory->id,
            'title' => 'Bincang Malam',
            'slug' => 'bincang-malam',
            'synopsis' => 'Sinopsis bincang malam',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertDatabaseHas('broadcasts', ['title' => 'Bincang Malam']);
        $this->assertEquals('Talkshow', $broadcast->broadcastCategory->name);
    }

    #[Test]
    public function it_can_create_jadwal_acara_with_correct_relations()
    {
        $bcCategory = BroadcastCategory::create(['name' => 'Berita', 'slug' => 'berita', 'color' => 'blue']);
        $jadwalCategory = JadwalCategory::create(['name' => 'Senin', 'slug' => 'senin', 'color' => 'green', 'order' => 1]);

        $jadwal = JadwalAcara::create([
            'title' => 'Berita Pagi',
            'slug' => 'berita-pagi',
            'start_time' => '06:00',
            'end_time' => '07:00',
            'broadcast_category_id' => $bcCategory->id,
            'jadwal_category_id' => $jadwalCategory->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('jadwal_acaras', ['title' => 'Berita Pagi']);

        // Eager load cast test
        $this->assertEquals('Senin', $jadwal->jadwalCategory->name);
        $this->assertEquals('Berita', $jadwal->broadcastCategory->name);
    }

    #[Test]
    public function it_can_create_info_magang_and_casts_boolean_correctly()
    {
        $magang = InfoMagang::create([
            'title' => 'Lowongan Magang',
            'slug' => 'lowongan-magang',
            'description' => 'Deskripsi magang',
            'source_link' => 'http://example.com',
            'is_active' => 1,
        ]);

        $this->assertDatabaseHas('info_magangs', ['title' => 'Lowongan Magang']);

        // Memastikan Laravel casting berfungsi (merubah 1/0 ke tipe boolean native)
        $this->assertTrue($magang->is_active);
    }
}

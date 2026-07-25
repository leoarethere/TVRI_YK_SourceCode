<?php

namespace Tests\Feature;

use App\Models\Broadcast;
use App\Models\BroadcastCategory;
use App\Models\Category;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::factory()->create();
        Post::factory()->count(5)->create(['category_id' => $category->id]);

        $newsCategory = NewsCategory::factory()->create();
        News::factory()->count(3)->create(['news_category_id' => $newsCategory->id]);

        $broadcastCategory = BroadcastCategory::factory()->create();
        Broadcast::factory()->count(2)->create(['broadcast_category_id' => $broadcastCategory->id]);
    }

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_homepage_has_correct_title(): void
    {
        $response = $this->get('/');
        $response->assertSee('TVRI');
    }

    public function test_posts_page_loads(): void
    {
        $response = $this->get(route('postingan.index'));
        $response->assertStatus(200);
    }

    public function test_posts_page_shows_posts(): void
    {
        $response = $this->get(route('postingan.index'));
        $response->assertViewHas('posts');
        $this->assertGreaterThan(0, $response->viewData('posts')->total());
    }

    public function test_post_detail_page_loads(): void
    {
        $post = Post::first();
        $response = $this->get(route('postingan.show', $post));
        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_post_detail_has_seo_meta(): void
    {
        $post = Post::first();
        $response = $this->get(route('postingan.show', $post));
        $response->assertSee('<title>', false);
    }

    public function test_news_page_loads(): void
    {
        $response = $this->get(route('berita.index'));
        $response->assertStatus(200);
    }

    public function test_news_page_shows_news(): void
    {
        $response = $this->get(route('berita.index'));
        $response->assertViewHas('news');
        $this->assertGreaterThan(0, $response->viewData('news')->total());
    }

    public function test_news_detail_page_loads(): void
    {
        $news = News::first();
        $response = $this->get(route('berita.show', $news));
        $response->assertStatus(200);
        $response->assertSee($news->title);
    }

    public function test_news_detail_has_structured_data(): void
    {
        $news = News::first();
        $response = $this->get(route('berita.show', $news));
        $response->assertSee('NewsArticle');
    }

    public function test_broadcasts_page_loads(): void
    {
        $response = $this->get(route('penyiaran.index'));
        $response->assertStatus(200);
    }

    public function test_broadcasts_page_shows_broadcasts(): void
    {
        $response = $this->get(route('penyiaran.index'));
        $response->assertViewHas('broadcasts');
        $this->assertGreaterThan(0, $response->viewData('broadcasts')->total());
    }

    public function test_visi_misi_page_loads(): void
    {
        $response = $this->get(route('visi-misi'));
        $response->assertStatus(200);
    }

    public function test_sejarah_page_loads(): void
    {
        $response = $this->get(route('sejarah'));
        $response->assertStatus(200);
    }

    public function test_ppid_page_loads(): void
    {
        $response = $this->get(route('ppid.index'));
        $response->assertStatus(200);
    }

    public function test_streaming_page_loads(): void
    {
        $response = $this->get(route('streaming'));
        $response->assertStatus(200);
    }

    public function test_info_magang_page_loads(): void
    {
        $response = $this->get(route('info-magang.index'));
        $response->assertStatus(200);
    }

    public function test_info_kunjungan_page_loads(): void
    {
        $response = $this->get(route('info-kunjungan.index'));
        $response->assertStatus(200);
    }

    public function test_hymne_tvri_page_loads(): void
    {
        $response = $this->get(route('himne-tvri.index'));
        $response->assertStatus(200);
    }

    public function test_info_rb_page_loads(): void
    {
        $response = $this->get(route('info-rb.index'));
        $response->assertStatus(200);
    }

    public function test_all_page_responses_are_not_server_errors(): void
    {
        $routes = [
            '/',
            route('postingan.index'),
            route('berita.index'),
            route('penyiaran.index'),
            route('visi-misi'),
            route('sejarah'),
            route('ppid.index'),
            route('streaming'),
            route('info-magang.index'),
            route('info-kunjungan.index'),
            route('himne-tvri.index'),
            route('info-rb.index'),
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $this->assertTrue(
                $response->status() < 500,
                "Route {$route} returned status {$response->status()}"
            );
        }
    }
}

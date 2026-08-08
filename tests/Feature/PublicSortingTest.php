<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_posts_can_be_sorted_by_title(): void
    {
        Post::factory()->create(['title' => 'Zulu', 'slug' => 'zulu']);
        Post::factory()->create(['title' => 'Alpha', 'slug' => 'alpha']);

        $posts = $this->get(route('postingan.index', ['sort' => 'title_asc']))
            ->assertOk()
            ->viewData('posts');

        $this->assertSame(['Alpha', 'Zulu'], $posts->pluck('title')->all());
    }

    public function test_news_can_be_sorted_by_popularity(): void
    {
        News::factory()->create(['title' => 'Less Popular', 'slug' => 'less-popular', 'views' => 2]);
        News::factory()->create(['title' => 'Most Popular', 'slug' => 'most-popular', 'views' => 20]);

        $news = $this->get(route('berita.index', ['sort' => 'popular']))
            ->assertOk()
            ->viewData('news');

        $this->assertSame(['Most Popular', 'Less Popular'], $news->pluck('title')->all());
    }
}

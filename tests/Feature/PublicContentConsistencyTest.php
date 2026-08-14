<?php

namespace Tests\Feature;

use App\Models\Broadcast;
use App\Models\History;
use App\Models\News;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_future_publications_are_hidden_from_lists_and_detail_pages(): void
    {
        $post = Post::factory()->create([
            'title' => 'Posting Terjadwal Rahasia',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);
        $news = News::factory()->create([
            'title' => 'Berita Terjadwal Rahasia',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);
        $broadcast = Broadcast::factory()->create([
            'title' => 'Program Terjadwal Rahasia',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('postingan.index'))
            ->assertOk()
            ->assertDontSee($post->title);
        $this->get(route('berita.index'))
            ->assertOk()
            ->assertDontSee($news->title);
        $this->get(route('penyiaran.index'))
            ->assertOk()
            ->assertDontSee($broadcast->title);

        $this->get(route('postingan.show', $post))->assertNotFound();
        $this->get(route('berita.show', $news))->assertNotFound();
        $this->get(route('penyiaran.show', $broadcast))->assertNotFound();
    }

    public function test_history_requires_a_reached_publication_date(): void
    {
        $visible = History::create([
            'title' => 'Sejarah Terbit',
            'content' => 'Sudah dapat dibaca.',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
        History::create([
            'title' => 'Sejarah Tanpa Tanggal',
            'content' => 'Belum dapat dibaca.',
            'status' => 'published',
            'published_at' => null,
        ]);
        History::create([
            'title' => 'Sejarah Masa Depan',
            'content' => 'Belum waktunya dibaca.',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $response = $this->get(route('sejarah'))->assertOk();

        $this->assertSame([$visible->id], $response->viewData('histories')->pluck('id')->all());
    }

    public function test_future_publication_cannot_receive_comments(): void
    {
        $post = Post::factory()->create([
            'status' => 'published',
            'published_at' => now()->addHour(),
        ]);

        $this->post(route('komentar.store'), [
            'name' => 'Pengunjung',
            'email' => 'pengunjung@example.com',
            'body' => 'Komentar sebelum jadwal publikasi.',
            'commentable_type' => 'post',
            'commentable_id' => $post->id,
        ])->assertSessionHasErrors('error');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_search_treats_percent_and_underscore_as_literal_characters(): void
    {
        $postLiteral = Post::factory()->create(['title' => 'Capaian 100% TVRI']);
        Post::factory()->create(['title' => 'Capaian Seratus TVRI']);

        $newsLiteral = News::factory()->create(['title' => 'Kode_Acara TVRI']);
        News::factory()->create(['title' => 'KodeXAcara TVRI']);

        $broadcastLiteral = Broadcast::factory()->create(['title' => 'Program 50% Spesial']);
        Broadcast::factory()->create(['title' => 'Program Umum Spesial']);

        $postResponse = $this->get(route('postingan.index', ['search' => '%']))->assertOk();
        $this->assertSame([$postLiteral->id], $postResponse->viewData('posts')->pluck('id')->all());

        $newsResponse = $this->get(route('berita.index', ['search' => '_']))->assertOk();
        $this->assertSame([$newsLiteral->id], $newsResponse->viewData('news')->pluck('id')->all());

        $broadcastResponse = $this->get(route('penyiaran.index', ['search' => '%']))->assertOk();
        $this->assertSame([$broadcastLiteral->id], $broadcastResponse->viewData('broadcasts')->pluck('id')->all());
    }

    public function test_public_search_rejects_unreasonably_long_input(): void
    {
        $this->from(route('postingan.index'))
            ->get(route('postingan.index', ['search' => str_repeat('a', 101)]))
            ->assertRedirect(route('postingan.index'))
            ->assertSessionHasErrors('search');
    }
}

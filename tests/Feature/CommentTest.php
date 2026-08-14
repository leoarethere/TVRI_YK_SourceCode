<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private News $news;

    private Post $post;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();

        $newsCategory = NewsCategory::factory()->create();
        $this->news = News::factory()->create(['news_category_id' => $newsCategory->id]);

        $category = Category::factory()->create();
        $this->post = Post::factory()->create(['category_id' => $category->id]);
    }

    public function test_guest_can_submit_comment_on_news(): void
    {
        $response = $this->post(route('komentar.store'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'body' => 'Great article! Very informative.',
            'commentable_type' => 'news',
            'commentable_id' => $this->news->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'body' => 'Great article! Very informative.',
            'status' => 'pending',
            'commentable_id' => $this->news->id,
            'commentable_type' => \App\Models\News::class,
        ]);
    }

    public function test_guest_can_submit_comment_on_post(): void
    {
        $response = $this->post(route('komentar.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'body' => 'Nice post!',
            'commentable_type' => 'post',
            'commentable_id' => $this->post->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('comments', [
            'name' => 'Jane Doe',
            'commentable_type' => \App\Models\Post::class,
        ]);
    }

    public function test_rating_aggregate_does_not_inherit_comment_ordering(): void
    {
        $this->post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->post->comments()->create([
            'name' => 'Reviewer',
            'email' => 'reviewer@example.com',
            'body' => 'Bagus.',
            'rating' => 5,
            'status' => 'approved',
        ]);

        $queries = [];
        DB::listen(function ($query) use (&$queries): void {
            $queries[] = $query->sql;
        });

        $this->get(route('postingan.show', $this->post))->assertOk();

        $aggregateQuery = collect($queries)->first(
            fn (string $sql): bool => str_contains(strtolower($sql), 'avg(rating)')
        );

        $this->assertNotNull($aggregateQuery);
        $this->assertStringNotContainsString('order by', strtolower($aggregateQuery));
    }

    public function test_comment_rejects_invalid_commentable_type(): void
    {
        $response = $this->post(route('komentar.store'), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'body' => 'Trying to inject.',
            'commentable_type' => 'App\Models\User',
            'commentable_id' => 1,
        ]);

        $response->assertSessionHasErrors('commentable_type');
    }

    public function test_comment_rejects_nonexistent_content(): void
    {
        $response = $this->post(route('komentar.store'), [
            'name' => 'Ghost',
            'email' => 'ghost@example.com',
            'body' => 'Comment on nothing.',
            'commentable_type' => 'news',
            'commentable_id' => 99999,
        ]);

        $response->assertSessionHasErrors('error');
    }

    public function test_comment_detects_spam_with_website_field(): void
    {
        $response = $this->post(route('komentar.store'), [
            'name' => 'Spammer',
            'email' => 'spam@example.com',
            'body' => 'Check this out!',
            'website' => 'http://spam-site.com',
            'commentable_type' => 'news',
            'commentable_id' => $this->news->id,
        ]);

        $response->assertSessionHasErrors('error');
    }

    public function test_comment_requires_validation(): void
    {
        $response = $this->post(route('komentar.store'), []);
        $response->assertSessionHasErrors(['name', 'email', 'body', 'commentable_type', 'commentable_id']);
    }

    public function test_admin_can_view_comments_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.komentar.index'));

        $this->post(route('komentar.store'), [
            'name' => 'User',
            'email' => 'user@example.com',
            'body' => 'Pending comment.',
            'commentable_type' => 'news',
            'commentable_id' => $this->news->id,
        ]);

        $comment = \App\Models\Comment::first();

        $response = $this->actingAs($this->admin)->put(route('dashboard.komentar.setujui', $comment));
        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_reject_comment(): void
    {
        $this->post(route('komentar.store'), [
            'name' => 'User',
            'email' => 'user@example.com',
            'body' => 'Another comment.',
            'commentable_type' => 'news',
            'commentable_id' => $this->news->id,
        ]);

        $comment = \App\Models\Comment::first();

        $response = $this->actingAs($this->admin)->put(route('dashboard.komentar.tolak', $comment));
        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'rejected',
        ]);
    }

    public function test_admin_can_delete_comment(): void
    {
        $this->post(route('komentar.store'), [
            'name' => 'User',
            'email' => 'user@example.com',
            'body' => 'Delete me.',
            'commentable_type' => 'news',
            'commentable_id' => $this->news->id,
        ]);

        $comment = \App\Models\Comment::first();

        $response = $this->actingAs($this->admin)->delete(route('dashboard.komentar.destroy', $comment));
        $response->assertRedirect();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Broadcast;
use App\Models\BroadcastCategory;
use App\Models\Category;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Post;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Pastikan ada user admin
        $user = User::first() ?? User::factory()->create();

        // 1. Buat Kategori Post
        $postCategory = Category::firstOrCreate(
            ['slug' => 'informasi-umum'],
            ['name' => 'Informasi Umum', 'color' => 'blue']
        );

        // 2. Buat Kategori News
        $newsCategory = NewsCategory::firstOrCreate(
            ['slug' => 'berita-terkini'],
            ['name' => 'Berita Terkini']
        );

        // 3. Buat Kategori Broadcast
        $broadcastCategory = BroadcastCategory::firstOrCreate(
            ['slug' => 'dialog-publik'],
            ['name' => 'Dialog Publik', 'color' => 'red']
        );

        // 4. Buat Data Post
        for ($i = 1; $i <= 3; $i++) {
            $post = Post::create([
                'user_id' => $user->id,
                'category_id' => $postCategory->id,
                'title' => 'Postingan Dummy '.$i,
                'slug' => 'postingan-dummy-'.$i,
                'body' => '<p>'.implode('</p><p>', $faker->paragraphs(3)).'</p>',
                'excerpt' => $faker->sentence(),
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 10)),
            ]);
            $this->seedComments($post, $faker);
        }

        // 5. Buat Data News
        for ($i = 1; $i <= 3; $i++) {
            $news = News::create([
                'user_id' => $user->id,
                'news_category_id' => $newsCategory->id,
                'title' => 'Berita Dummy '.$i,
                'slug' => 'berita-dummy-'.$i,
                'body' => '<p>'.implode('</p><p>', $faker->paragraphs(3)).'</p>',
                'excerpt' => $faker->sentence(),
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 10)),
            ]);
            $this->seedComments($news, $faker);
        }

        // 6. Buat Data Broadcast
        for ($i = 1; $i <= 3; $i++) {
            $broadcast = Broadcast::create([
                'user_id' => $user->id,
                'broadcast_category_id' => $broadcastCategory->id,
                'title' => 'Program Siaran Dummy '.$i,
                'slug' => 'siaran-dummy-'.$i,
                'synopsis' => $faker->paragraph(),
                'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 10)),
                'is_active' => true,
            ]);
            $this->seedComments($broadcast, $faker);
        }
    }

    private function seedComments($model, $faker)
    {
        // Tambahkan 2-5 komentar per entitas
        $commentCount = rand(2, 5);
        for ($j = 0; $j < $commentCount; $j++) {
            $model->comments()->create([
                'name' => $faker->name,
                'email' => $faker->safeEmail,
                'body' => $faker->sentence(rand(5, 15)),
                'rating' => rand(3, 5),
                'status' => 'approved',
            ]);
        }
    }
}

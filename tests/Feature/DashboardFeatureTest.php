<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    private $nonAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->nonAdmin = User::factory()->create(['role' => 'pegawai']);
    }

    #[Test]
    public function non_admin_cannot_access_dashboard()
    {
        // Mencoba mengakses rute dasbor postingan sebagai pengguna biasa
        $response = $this->actingAs($this->nonAdmin)->get('/dashboard/postingan');

        // Memastikan dialihkan ke beranda (atau dilarang) dengan Error Rate sangat rendah (bukan 500)
        // Jika ada handler untuk redirect atau abort 403, ini harus disesuaikan. Kita test 302 or 403.
        $this->assertTrue(in_array($response->status(), [302, 403, 404]));
    }

    #[Test]
    public function admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)->get('/dashboard/postingan');

        $response->assertStatus(200);
    }

    #[Test]
    public function creating_post_requires_valid_data()
    {
        // Kirim form kosong (tanpa judul, kategori, dll)
        $response = $this->actingAs($this->admin)->post('/dashboard/postingan', []);

        // Memastikan sistem merespon dengan pesan error validasi (Session Errors), bukan Server Error 500.
        $response->assertSessionHasErrors(['title', 'category_id', 'body']);
    }

    #[Test]
    public function admin_can_create_a_post()
    {
        $category = Category::create(['name' => 'Berita', 'slug' => 'berita', 'color' => 'blue']);

        $response = $this->actingAs($this->admin)->post('/dashboard/postingan', [
            'title' => 'Berita Baru',
            'slug' => 'berita-baru',
            'category_id' => $category->id,
            'body' => 'Isi berita.',
            'action' => 'publish',
            // Image diabaikan karena nullable
        ]);

        // Dialihkan ke daftar postingan dengan pesan sukses
        $response->assertRedirect('/dashboard/postingan');
        $response->assertSessionHas('success');

        // Pastikan masuk ke database
        $this->assertDatabaseHas('posts', [
            'title' => 'Berita Baru',
            'slug' => 'berita-baru',
        ]);
    }
}

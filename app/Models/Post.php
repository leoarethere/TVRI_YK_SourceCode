<?php

namespace App\Models;

use App\Models\Concerns\HasPublicPublicationStatus;
use App\Models\Concerns\InvalidatesHomepageDataCache;
use App\Support\LikeSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory, HasPublicPublicationStatus, InvalidatesHomepageDataCache;

    /**
     * Boot model: Hapus komentar terkait saat postingan dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(function (Post $post) {
            $post->comments()->delete();
        });
    }

    protected $fillable = [
        'title', 'slug', 'user_id', 'category_id', 'body',
        'link_postingan', 'featured_image', 'excerpt',
        'status', 'published_at', 'meta_title', 'meta_description', 'views',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ✅ TAMBAHKAN FUNGSI INI UNTUK MEMPERBAIKI ERROR
    public function incrementViews(): void
    {
        // increment() adalah fungsi bawaan Eloquent untuk menambah nilai kolom angka secara otomatis (+1)
        $this->increment('views');
    }

    // ... (kode scopeFilter tetap sama seperti sebelumnya) ...
    public function scopeFilter(Builder $query, array $filters): void
    {
        // 1. Logika Search
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                LikeSearch::contains($q, 'title', $search);
                LikeSearch::contains($q, 'body', $search, 'or');
                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    LikeSearch::contains($userQuery, 'name', $search);
                })->orWhereHas('category', function ($catQuery) use ($search) {
                    LikeSearch::contains($catQuery, 'name', $search);
                });
            });
        });

        // 2. Filter Kategori
        $query->when($filters['category'] ?? false, function ($query, $category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        });

        // 3. Filter Author
        $query->when($filters['author'] ?? false, function ($query, $author) {
            $query->whereHas('user', function ($q) use ($author) {
                $q->where('username', $author);
            });
        });
    }

    /**
     * Dapatkan semua komentar untuk postingan ini.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }
}

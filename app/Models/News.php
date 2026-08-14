<?php

namespace App\Models;

use App\Models\Concerns\HasPublicPublicationStatus;
use App\Support\LikeSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class News extends Model
{
    use HasFactory, HasPublicPublicationStatus;

    /**
     * Boot model: Hapus komentar terkait saat berita dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(function (News $news) {
            $news->comments()->delete();
        });
    }

    // Menentukan kolom mana saja yang boleh diisi secara massal
    protected $fillable = [
        'title', 'slug', 'user_id', 'news_category_id', 'body',
        'link_berita', 'featured_image', 'excerpt',
        'status', 'published_at', 'meta_title', 'meta_description', 'views',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Relasi ke User (Penulis)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Kategori Berita
    // Kita sebut fungsinya 'newsCategory' agar jelas, dan definisikan FK-nya 'news_category_id'
    public function newsCategory(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    // Fungsi untuk menambah jumlah view
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    // LOGIKA FILTER & PENCARIAN (Adaptasi dari Post)
    public function scopeFilter(Builder $query, array $filters): void
    {
        // 1. Logika Search (Judul, Body, Penulis, Kategori)
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                LikeSearch::contains($q, 'title', $search);
                LikeSearch::contains($q, 'body', $search, 'or');
                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    LikeSearch::contains($userQuery, 'name', $search);
                })->orWhereHas('newsCategory', function ($catQuery) use ($search) {
                    LikeSearch::contains($catQuery, 'name', $search);
                });
            });
        });

        // 2. Filter Kategori
        $query->when($filters['category'] ?? false, function ($query, $category) {
            $query->whereHas('newsCategory', function ($q) use ($category) {
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
     * Dapatkan semua komentar untuk berita ini.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }
}

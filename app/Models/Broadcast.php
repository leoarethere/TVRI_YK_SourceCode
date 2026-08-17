<?php

namespace App\Models;

use App\Models\Concerns\HasPublicPublicationStatus;
use App\Models\Concerns\InvalidatesHomepageDataCache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Broadcast extends Model
{
    use HasFactory, HasPublicPublicationStatus, InvalidatesHomepageDataCache;

    /**
     * Boot model: Hapus komentar terkait saat program siaran dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(function (Broadcast $broadcast) {
            $broadcast->comments()->delete();

            if ($broadcast->poster) {
                Storage::disk('public')->delete($broadcast->poster);
            }
        });
    }

    protected $fillable = [
        'user_id',
        'broadcast_category_id',
        'title',
        'slug',
        'synopsis',
        'poster',
        'youtube_link', // Kolom database tempat user menaruh link
        'status',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function broadcastCategory(): BelongsTo
    {
        return $this->belongsTo(BroadcastCategory::class, 'broadcast_category_id');
    }

    /**
     * Accessor: youtube_embed_url
     * Dipanggil di blade dengan: $broadcast->youtube_embed_url
     * Mengubah berbagai format link YouTube menjadi link Embed yang valid.
     */
    protected function youtubeEmbedUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Ambil value langsung dari kolom 'youtube_link' di database
                $url = $attributes['youtube_link'] ?? null;

                if (! $url) {
                    return null;
                }

                // REGEX: Pola untuk mengambil Video ID (11 karakter) dari berbagai jenis link YouTube
                // Support:
                // - https://www.youtube.com/watch?v=dQw4w9WgXcQ
                // - https://youtu.be/dQw4w9WgXcQ
                // - https://www.youtube.com/embed/dQw4w9WgXcQ
                $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';

                if (preg_match($pattern, $url, $matches)) {
                    // $matches[1] berisi Video ID (contoh: dQw4w9WgXcQ)
                    return 'https://www.youtube.com/embed/'.$matches[1];
                }

                // Jika format tidak dikenali, kembalikan null agar tampilan fallback ke Poster
                return null;
            },
        );
    }

    /**
     * Dapatkan semua komentar untuk program siaran ini.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }
}

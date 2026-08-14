<?php

namespace App\Models;

use App\Models\Concerns\InvalidatesHomepageScheduleCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalAcara extends Model
{
    use HasFactory, InvalidatesHomepageScheduleCache;

    protected $fillable = [
        'title',
        'slug',
        'start_time',
        'end_time',
        'broadcast_category_id',
        'jadwal_category_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function broadcastCategory(): BelongsTo
    {
        return $this->belongsTo(BroadcastCategory::class, 'broadcast_category_id');
    }

    public function jadwalCategory(): BelongsTo
    {
        return $this->belongsTo(JadwalCategory::class, 'jadwal_category_id');
    }
}

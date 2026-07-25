<?php

namespace App\Models;

use App\Models\JadwalCategory;
use App\Models\BroadcastCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalAcara extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'start_time',
        'end_time',              // ✅ TAMBAHAN: Agar jam selesai bisa disimpan
        'broadcast_category_id',
        'jadwal_category_id',    // ✅ TAMBAHAN: Agar data Hari bisa disimpan
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

    public function jadwalCategory()
    {
        return $this->belongsTo(JadwalCategory::class, 'jadwal_category_id');
    }
}
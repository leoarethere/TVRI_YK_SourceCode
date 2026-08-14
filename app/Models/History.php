<?php

namespace App\Models;

use App\Models\Concerns\HasPublicPublicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory, HasPublicPublicationStatus;

    protected $fillable = [
        'title',
        'content',
        'image',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}

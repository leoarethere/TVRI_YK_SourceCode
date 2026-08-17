<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppid extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'source_link',  // ✅ Konsisten dengan database
        'cover_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

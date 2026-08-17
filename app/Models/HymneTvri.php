<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HymneTvri extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'info',
        'poster',
        'synopsis',
        'link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

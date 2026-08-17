<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoMagang extends Model
{
    use HasFactory;

    protected $table = 'info_magangs';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'source_link',
        'cover_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

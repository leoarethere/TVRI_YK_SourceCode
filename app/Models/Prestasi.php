<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'award_name',
        'type',
        'category',
        'year',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'year' => 'integer',
    ];
}

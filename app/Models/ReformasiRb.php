<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReformasiRb extends Model
{
    use HasFactory;

    protected $table = 'reformasi_rbs';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'file_link',
        'cover_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisiMisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'content',
        'image', // [BARU] Tambahkan ini
        'order',
        'is_active',
    ];
}

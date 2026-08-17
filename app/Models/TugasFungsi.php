<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Ganti nama class
class TugasFungsi extends Model
{
    use HasFactory;

    // Jika nama tabel di migrasi adalah 'tugas_fungsis', baris ini opsional.
    // Tapi jika Anda ingin nama tabel custom (misal: 'tugas_fungsi'), tambahkan:
    // protected $table = 'tugas_fungsi';

    protected $fillable = [
        'type', 'order', 'image', 'content', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

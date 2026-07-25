<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AksesInformasi extends Model
{
    use HasFactory;

    protected $table = 'akses_informasi';

    protected $fillable = ['type', 'label', 'url', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Ambil record untuk tipe 'internal'.
     */
    public static function internal(): self
    {
        return static::firstOrCreate(
            ['type' => 'internal'],
            ['label' => 'Akses Informasi Internal', 'url' => '#', 'is_active' => true]
        );
    }

    /**
     * Ambil record untuk tipe 'publik'.
     */
    public static function publik(): self
    {
        return static::firstOrCreate(
            ['type' => 'publik'],
            ['label' => 'Akses Informasi Publik', 'url' => '#', 'is_active' => true]
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoMagangFaq extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit (opsional, tapi praktik bagus)
    protected $table = 'info_magang_faqs';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'question',
        'answer',
        'order',
        'is_active',
    ];

    // Konversi tipe data otomatis
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];
}

<?php

namespace App\Models;

use App\Models\Concerns\InvalidatesHomepageDataCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    use HasFactory, InvalidatesHomepageDataCache;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_phone',
        'partnership_phone',
        'hotline_phone',
        'address',
        'email',
        'is_active',
        'google_maps_embed', // <--- Tambahkan ini
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
}

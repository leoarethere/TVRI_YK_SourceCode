<?php

namespace App\Models;

use App\Models\JadwalAcara;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color', 'order'];

    public function jadwalAcaras()
    {
        return $this->hasMany(JadwalAcara::class);
    }

    // Accessor warna (Sama seperti BroadcastCategory)
    protected function colorClasses(): Attribute
    {
        return Attribute::make(
            get: function () {
                $colors = [
                    'blue'   => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                    'green'  => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                    'red'    => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                    'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                    'gray'   => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                ];
                return $colors[$this->color] ?? $colors['gray'];
            }
        );
    }
}
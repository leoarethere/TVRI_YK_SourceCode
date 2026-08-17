<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color'];

    // Relasi: Satu kategori memiliki banyak berita
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    /**
     * Menghasilkan kelas CSS Tailwind yang sesuai berdasarkan kolom 'color'.
     * Digunakan untuk badge di dashboard (latar terang/gelap adaptif).
     */
    protected function colorClasses(): Attribute
    {
        return Attribute::make(
            get: function () {
                $colorName = $this->color ?? 'blue';

                $colorMap = [
                    'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                    'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                    'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                    'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                    'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                ];

                return $colorMap[$colorName] ?? $colorMap['blue'];
            }
        );
    }

    /**
     * Menghasilkan kelas CSS untuk badge solid (digunakan di atas gambar di frontend).
     */
    protected function colorSolidClasses(): Attribute
    {
        return Attribute::make(
            get: function () {
                $colorName = $this->color ?? 'blue';

                $colorMap = [
                    'blue' => 'bg-blue-600',
                    'red' => 'bg-red-600',
                    'green' => 'bg-green-600',
                    'yellow' => 'bg-yellow-500',
                    'purple' => 'bg-purple-600',
                    'gray' => 'bg-gray-500',
                ];

                return $colorMap[$colorName] ?? $colorMap['blue'];
            }
        );
    }
}

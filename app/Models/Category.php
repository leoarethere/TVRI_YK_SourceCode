<?php

namespace App\Models;

use App\Models\Concerns\InvalidatesHomepageDataCache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, InvalidatesHomepageDataCache;

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    protected function colorClasses(): Attribute
    {
        return Attribute::make(
            get: function () {
                $colorName = $this->color ?? 'gray';

                $colorMap = [
                    'blue' => 'bg-blue-100 text-gray-800 dark:bg-blue-700/80 dark:text-gray-800',
                    'pink' => 'bg-pink-100 text-gray-800 dark:bg-pink-700/80 dark:text-gray-800',
                    'green' => 'bg-green-100 text-gray-800 dark:bg-green-700/80 dark:text-gray-800',
                    'yellow' => 'bg-yellow-100 text-gray-800 dark:bg-yellow-700/80 dark:text-gray-800',
                    'indigo' => 'bg-indigo-100 text-gray-800 dark:bg-indigo-700/80 dark:text-gray-800',
                    'purple' => 'bg-purple-100 text-gray-800 dark:bg-purple-700/80 dark:text-gray-800',
                    'red' => 'bg-red-100 text-gray-800 dark:bg-red-700/80 dark:text-gray-800',
                    'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-600/80 dark:text-gray-800',
                ];

                return $colorMap[$colorName] ?? $colorMap['gray'];
            }
        );
    }

    protected function colorSolidClasses(): Attribute
    {
        return Attribute::make(
            get: function () {
                $colorName = $this->color ?? 'blue';

                $colorMap = [
                    'blue' => 'bg-blue-600',
                    'pink' => 'bg-pink-600',
                    'green' => 'bg-green-600',
                    'yellow' => 'bg-yellow-500',
                    'indigo' => 'bg-indigo-600',
                    'purple' => 'bg-purple-600',
                    'red' => 'bg-red-600',
                    'gray' => 'bg-gray-500',
                ];

                return $colorMap[$colorName] ?? $colorMap['blue'];
            }
        );
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\InvalidatesHomepageScheduleCache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalCategory extends Model
{
    use HasFactory, InvalidatesHomepageScheduleCache;

    protected $fillable = ['name', 'slug', 'color', 'order'];

    public function jadwalAcaras(): HasMany
    {
        return $this->hasMany(JadwalAcara::class);
    }

    public function appliesToDay(string $daySlug): bool
    {
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $normalizedSlug = strtolower($this->slug);
        $dayIndex = array_search(strtolower($daySlug), $days, true);

        if ($dayIndex === false) {
            return false;
        }

        if ($normalizedSlug === $days[$dayIndex]) {
            return true;
        }

        $range = explode('-', $normalizedSlug);

        if (count($range) !== 2) {
            return false;
        }

        $startIndex = array_search($range[0], $days, true);
        $endIndex = array_search($range[1], $days, true);

        if ($startIndex === false || $endIndex === false) {
            return false;
        }

        if ($startIndex <= $endIndex) {
            return $dayIndex >= $startIndex && $dayIndex <= $endIndex;
        }

        return $dayIndex >= $startIndex || $dayIndex <= $endIndex;
    }

    // Accessor warna (Sama seperti BroadcastCategory)
    protected function colorClasses(): Attribute
    {
        return Attribute::make(
            get: function () {
                $colors = [
                    'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                    'pink' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
                    'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                    'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                    'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                    'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                    'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                ];

                return $colors[$this->color] ?? $colors['gray'];
            }
        );
    }
}

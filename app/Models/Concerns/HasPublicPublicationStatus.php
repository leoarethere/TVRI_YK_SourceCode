<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasPublicPublicationStatus
{
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }
}

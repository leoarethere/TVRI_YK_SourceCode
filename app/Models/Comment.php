<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'body',
        'rating',
        'status',
        'commentable_id',
        'commentable_type',
    ];

    protected $casts = [
        'rating' => 'integer',
        'status' => 'string',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }
}

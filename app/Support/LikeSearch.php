<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

final class LikeSearch
{
    public static function contains(Builder $query, string $column, string $value, string $boolean = 'and'): Builder
    {
        $escaped = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value);
        $wrappedColumn = $query->getQuery()->getGrammar()->wrap($column);

        return $query->whereRaw(
            "{$wrappedColumn} LIKE ? ESCAPE '!'",
            ['%'.$escaped.'%'],
            $boolean,
        );
    }
}

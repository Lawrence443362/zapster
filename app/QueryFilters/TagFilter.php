<?php

namespace App\QueryFilters;

use Spatie\QueryBuilder\AllowedFilter;

class TagFilter
{
    public static function filters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('id'),
            AllowedFilter::callback(
                'date_from',
                fn($query, $value) =>
                $query->where('created_at', '>=', $value)
            ),
            AllowedFilter::callback(
                'date_to',
                fn($query, $value) =>
                $query->where('created_at', '<=', $value)
            ),
        ];
    }
}

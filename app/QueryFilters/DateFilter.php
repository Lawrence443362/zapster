<?php

namespace App\QueryFilters;

use Spatie\QueryBuilder\AllowedFilter;

class DateFilter
{
    public static function date_from(): AllowedFilter
    {
        return AllowedFilter::callback(
            'date_from',
            fn($query, $value) =>
            $query->where('created_at', '>=', $value)
        );
    }

    public static function date_to(): AllowedFilter
    {
        return AllowedFilter::callback(
            'date_to',
            fn($query, $value) =>
            $query->where('created_at', '<=', $value)
        );
    }
}

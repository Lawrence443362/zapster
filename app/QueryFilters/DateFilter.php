<?php

namespace App\QueryFilters;

use Spatie\QueryBuilder\AllowedFilter;

class DateFilter
{
    public static function DateFrom(): AllowedFilter
    {
        return AllowedFilter::callback(
            'date_from',
            fn($query, $value) =>
            $query->where('created_at', '>=', $value)
        );
    }

    public static function DateTo(): AllowedFilter
    {
        return AllowedFilter::callback(
            'date_to',
            fn($query, $value) =>
            $query->where('created_at', '<=', $value)
        );
    }
}

<?php

namespace App\QueryFilters;

use Spatie\QueryBuilder\AllowedFilter;

class PostFilter
{
    public static function filters(): array
    {
        return [
            AllowedFilter::partial('title'),
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_id'),
            DateFilter::date_from(),
            DateFilter::date_to()
        ];
    }
}

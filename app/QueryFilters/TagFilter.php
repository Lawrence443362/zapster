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
            DateFilter::date_from(),
            DateFilter::date_to()
        ];
    }
}

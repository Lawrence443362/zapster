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
            DateFilter::DateFrom(),
            DateFilter::DateTo(),
            self::tagFilter()
        ];
    }

    public static function tagFilter(): AllowedFilter
    {
        return AllowedFilter::callback('tags', function ($query, $value) {
            $tags = self::parseTags($value);

            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('name', $tags);
            }, '=', count($tags)); // пост должен иметь ВСЕ теги
        });
    }

    private static function parseTags(string|array $tags): array
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        return array_map('trim', $tags);
    }
}

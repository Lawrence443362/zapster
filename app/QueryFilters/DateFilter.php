<?php

namespace App\QueryFilters;

use Spatie\QueryBuilder\AllowedFilter;

/**
 * Фильтр по дате создания модели.
 *
 * Предоставляет фильтры для ограничения выборки по дате "от" и "до" поля created_at.
 */
class DateFilter
{
    /**
     * Создаёт AllowedFilter для фильтрации записей по дате "от" (created_at >= значение).
     *
     * Используется в Spatie\QueryBuilder для разрешения фильтра 'date_from'.
     *
     * @return AllowedFilter
     */
    public static function DateFrom(): AllowedFilter
    {
        return AllowedFilter::callback(
            'date_from',
            fn($query, $value) =>
                $query->where('created_at', '>=', $value)
        );
    }

    /**
     * Создаёт AllowedFilter для фильтрации записей по дате "до" (created_at <= значение).
     *
     * Используется в Spatie\QueryBuilder для разрешения фильтра 'date_to'.
     *
     * @return AllowedFilter
     */
    public static function DateTo(): AllowedFilter
    {
        return AllowedFilter::callback(
            'date_to',
            fn($query, $value) =>
                $query->where('created_at', '<=', $value)
        );
    }
}

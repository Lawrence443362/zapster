<?php

namespace App\QueryFilters;

use Spatie\QueryBuilder\AllowedFilter;

/**
 * Класс фильтров для модели Tag.
 *
 * Предоставляет фильтры для выборки тегов с использованием Spatie\QueryBuilder:
 * - частичный поиск по имени
 * - точное сравнение по ID
 * - фильтрация по дате создания (от и до)
 */
class TagFilter
{
    /**
     * Возвращает массив фильтров для использования в QueryBuilder.
     *
     * @return AllowedFilter[]
     */
    public static function filters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('id'),
            DateFilter::DateFrom(),
            DateFilter::DateTo()
        ];
    }
}

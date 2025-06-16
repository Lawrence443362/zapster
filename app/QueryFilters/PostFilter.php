<?php

namespace App\QueryFilters;

use Spatie\QueryBuilder\AllowedFilter;

/**
 * Класс фильтров для модели Post.
 *
 * Предоставляет набор фильтров для использования с Spatie\QueryBuilder,
 * включая фильтрацию по заголовку, ID, пользователю, дате и тегам.
 */
class PostFilter
{
    /**
     * Возвращает массив фильтров для использования в QueryBuilder.
     *
     * Включает:
     * - частичный поиск по полю title
     * - точное сравнение по id и user_id
     * - фильтрацию по дате создания (от и до)
     * - фильтрацию по тегам (пост должен иметь все указанные теги)
     *
     * @return AllowedFilter[]
     */
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

    /**
     * Создаёт фильтр для выборки постов по тегам.
     *
     * Ожидает параметр 'tags' в виде строки с тегами через запятую или массива.
     * Возвращает записи, которые имеют все указанные теги.
     *
     * @return AllowedFilter
     */
    public static function tagFilter(): AllowedFilter
    {
        return AllowedFilter::callback('tags', function ($query, $value) {
            $tags = self::parseTags($value);

            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('name', $tags);
            }, '=', count($tags)); // Пост должен иметь все теги
        });
    }

    /**
     * Преобразует входные данные тегов в массив строк.
     *
     * Принимает строку с тегами через запятую или массив тегов,
     * возвращает массив тегов без пробелов по краям.
     *
     * @param string|string[] $tags Строка с тегами через запятую или массив тегов
     * @return string[] Массив очищенных тегов
     */
    private static function parseTags(string|array $tags): array
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        return array_map('trim', $tags);
    }
}

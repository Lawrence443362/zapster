<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TagResource;
use App\Models\Tag;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\QueryFilters\TagFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TagController extends Controller
{
    /**
     * Отображает список ресурсов (тегов) с поддержкой сортировки и фильтрации.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @description
     * Метод возвращает постраничный список тегов.
     * Можно указать параметр `per_page` в запросе для изменения количества элементов на странице (по умолчанию 15).
     *
     * Поддерживается сортировка по полям:
     * - `name` — по имени тега в алфавитном порядке;
     * - `created_at` — по дате создания тега;
     * - `id` — по идентификатору тега.
     *
     * Для сортировки можно использовать знак минус (-) перед именем поля, чтобы изменить порядок на обратный (по убыванию).
     *
     * Поддерживается фильтрация через кастомный фильтр TagFilter.
     *
     * Доступные фильтры:
     * - partial('name') — частичное совпадение по имени тега (LIKE %value%)
     * - exact('id') — точное совпадение по идентификатору тега
     * - date_from — фильтр по дате создания "с" (начальная дата)
     * - date_to — фильтр по дате создания "по" (конечная дата)
     *
     * Сортировка работает для следующих полей(Если мы пишем минус, перед полем сортировки, то сортировка будет идти по убыванию):
     * - name
     * - created_at
     * - id
     *
     * Использование в запросе (через QueryBuilder):
     * - ?filter[name]=some — вернёт теги, у которых в имени есть "some"
     * - ?filter[id]=5 — вернёт тег с id = 5
     * - ?filter[created_from]=2024-01-01 — теги, созданные начиная с 1 января 2024
     * - ?filter[created_to]=2024-01-31 — теги, созданные до 31 января 2024 включительно
     *
     * Сочетание фильтров возможно, например:
     * ?filter[name]=rock&filter[created_from]=2024-01-01&filter[created_to]=2024-01-31
     *
     * Пример запроса с сортировкой и фильтрацией:
     * GET /api/v1/tags?per_page=10&sort=-created_at&filter[name]=rock
     */
    public function index()
    {
        $per_page = request("per_page", 15);
        $tags = QueryBuilder::for(Tag::class)
            ->allowedSorts(["name", "created_at", "id"])
            ->allowedFilters(TagFilter::filters())
            ->paginate($per_page);

        return TagResource::collection($tags);
    }

    /**
     * Создает новый ресурс (тег).
     *
     * @param StoreTagRequest $request Валидированный запрос с данными тега.
     * @return TagResource
     *
     * @description
     * Если тег с такими параметрами уже существует, возвращает существующий.
     * Иначе создаёт новый тег.
     */
    public function store(StoreTagRequest $request)
    {
        $tag = Tag::firstOrCreate($request->validated());

        return new TagResource($tag);
    }

    /**
     * Отображает конкретный ресурс (тег).
     *
     * @param Tag $tag Модель тега.
     * @return TagResource
     *
     * @description
     * Возвращает детальную информацию по тегу.
     */
    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }

    /**
     * Обновляет указанный ресурс (тег).
     *
     * @param UpdateTagRequest $request Валидированные данные для обновления.
     * @param Tag $tag Модель тега для обновления.
     * @return TagResource
     *
     * @description
     * Обновляет поля тега согласно переданным данным.
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return new TagResource($tag);
    }

    /**
     * Удаляет указанный ресурс (тег).
     *
     * @param Tag $tag Модель тега для удаления.
     * @return \Illuminate\Http\JsonResponse
     *
     * @description
     * Удаляет тег из базы данных.
     * Возвращает JSON-ответ с подтверждением удаления.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'message' => 'Tag removed'
        ]);
    }
}

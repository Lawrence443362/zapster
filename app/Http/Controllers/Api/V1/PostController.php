<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\AttachAudioToPostRequest;
use App\Http\Requests\Post\DeletePostRequest;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\QueryFilters\PostFilter;
use App\Services\PostService;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Контроллер управления постами API v1.
 *
 * Этот контроллер предоставляет методы для:
 * - отображения списка постов с фильтрацией и сортировкой;
 * - создания, обновления и удаления постов;
 * - привязки аудиофайлов;
 * - получения одного поста.
 *
 * Использует сервисный слой PostService.
 */
class PostController extends Controller
{
    /**
     * Сервис работы с постами.
     *
     * @var \App\Services\PostService
     */
    public PostService $service;

    /**
     * Инициализирует контроллер с сервисом постов.
     *
     * @param  \App\Services\PostService  $service
     */
    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    /**
     * Отображает список постов с поддержкой пагинации, фильтрации и сортировки.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @description
     * Метод возвращает постраничный список постов.
     * Можно указать параметр `per_page` в запросе для изменения количества элементов на странице (по умолчанию 15).
     *
     * Подгружаются связи:
     * - `tags` — теги поста;
     * - `user` — автор поста (id, name);
     * - `audio` — привязанное аудио, если есть.
     *
     * Поддерживается фильтрация через кастомный фильтр `PostFilter`.
     * Доступные фильтры:
     * - `title` (partial) — частичное совпадение по заголовку;
     * - `id` (exact) — точное совпадение по идентификатору;
     * - `user_id` (exact) — точное совпадение по пользователю;
     * - `tags` — фильтрация по названиям тегов: `?filter[tags]=music,rock`;
     * - `date_from` — посты, созданные после указанной даты;
     * - `date_to` — посты, созданные до указанной даты.
     *
     * Сортировка возможна по следующим полям:
     * - `id`
     * - `title`
     * - `created_at`
     *
     * Префикс `-` в сортировке означает порядок по убыванию:
     * - `?sort=-created_at` — сначала новые посты.
     *
     * Примеры запросов:
     * - `?filter[title]=Laravel` — посты с заголовком, содержащим "Laravel"
     * - `?filter[tags]=music,rock` — посты с тегами "music" или "rock"
     * - `?sort=-created_at&per_page=20` — новые посты, по 20 на страницу
     */
    public function index()
    {
        $per_page = request('per_page', 15);
        $query = Post::with(["tags", "user:id,name", "audio"]);
        $posts = QueryBuilder::for($query)
            ->allowedSorts(["id", "title", "created_at"])
            ->allowedFilters(PostFilter::filters())
            ->paginate($per_page);

        return PostResource::collection($posts);
    }

    /**
     * Создаёт новый пост.
     *
     * @param  \App\Http\Requests\Post\StorePostRequest  $request
     * @return \App\Http\Resources\V1\PostResource
     *
     * @description
     * Метод создаёт новый пост от имени текущего пользователя.
     * Валидация данных выполняется через StorePostRequest.
     * После создания загружаются связанные теги и пользователь.
     */
    public function store(StorePostRequest $request)
    {
        $post = $this->service->store($request->user(), $request->validated());

        return new PostResource($post->load(['user:id,name', 'tags']));
    }

    /**
     * Привязывает аудиофайл к существующему посту.
     *
     * @param  \App\Http\Requests\Post\AttachAudioToPostRequest  $request
     * @param  string  $id
     * @return \App\Http\Resources\V1\PostResource
     *
     * @description
     * Метод загружает и привязывает аудиофайл к посту по его идентификатору.
     * Предварительно загружаются связи: пользователь, теги и текущее аудио.
     */
    public function attachAudio(AttachAudioToPostRequest $request, string $id)
    {
        $post = Post::with(['user:id,name', 'tags:id,name', 'audio'])->find($id);

        $this->service->attachAudio($post, $request->file('audio'));

        return new PostResource($post->load(['user:id,name', 'tags', 'audio']));
    }

    /**
     * Отображает информацию о конкретном посте.
     *
     * @param  int  $id
     * @return \App\Http\Resources\V1\PostResource
     *
     * @description
     * Метод возвращает подробную информацию о посте по его идентификатору.
     * Подгружаются связи: автор (user), теги и привязанное аудио.
     */
    public function show(int $id)
    {
        $post = Post::with(['user:id,name', 'tags:id,name', 'audio'])->findOrFail($id);

        return new PostResource($post);
    }

    /**
     * Обновляет существующий пост.
     *
     * @param  \App\Http\Requests\Post\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \App\Http\Resources\V1\PostResource
     *
     * @description
     * Метод обновляет пост с новыми данными, переданными пользователем.
     * После обновления подгружаются связи: автор, теги и аудио.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post = $this->service->update($request->user(), $post, $request->validated());

        return new PostResource($post->load(['user:id,name', 'tags', 'audio']));
    }

    /**
     * Удаляет указанный пост.
     *
     * @param  \App\Http\Requests\Post\DeletePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     *
     * @description
     * Метод удаляет пост.
     * Возвращает сообщение об успешном удалении.
     */
    public function destroy(DeletePostRequest $request, Post $post)
    {
        $post->delete();

        return response()->json([
            "message" => "Post removed"
        ]);
    }
}

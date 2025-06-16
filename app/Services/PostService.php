<?php

namespace App\Services;

use App\Events\AudioAttachedToPostEvent;
use App\Models\AudioFile;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для управления постами.
 *
 * Обеспечивает логику создания, обновления постов и прикрепления аудиофайлов.
 */
class PostService
{
    /**
     * Сервис для работы с аудиофайлами.
     *
     * @var AudioFileService
     */
    public AudioFileService $audioFileService;

    /**
     * Конструктор.
     *
     * @param AudioFileService $audioFileService Сервис для обработки аудиофайлов
     */
    public function __construct(AudioFileService $audioFileService)
    {
        $this->audioFileService = $audioFileService;
    }

    /**
     * Создаёт новый пост, связывает его с пользователем, создаёт и прикрепляет теги.
     *
     * Операция выполняется в транзакции.
     *
     * @param User $user Пользователь, которому принадлежит пост
     * @param array $validated Валидационные данные поста, включая ключ 'tags' с тегами
     * @return Post Созданный пост
     */
    public function store(User $user, array $validated): Post
    {
        $post = new Post($validated);
        $post->user()->associate($user);

        return DB::transaction(function () use ($post, $validated) {
            $tags = Tag::createAllNewTags($validated["tags"]);
            $post->save();
            $post->attachTags($tags);

            return $post;
        });
    }

    /**
     * Обновляет существующий пост и его теги.
     *
     * Выполняется в транзакции.
     *
     * @param User $user Пользователь, владеющий постом (не используется напрямую, но может быть полезен)
     * @param Post $post Пост для обновления
     * @param array $validated Обновлённые данные поста, включая ключ 'tags' с тегами
     * @return Post Обновлённый пост
     */
    public function update(User $user, Post $post, array $validated): Post
    {
        return DB::transaction(function () use ($post, $validated) {
            $post->update($validated);

            $tags = Tag::createAllNewTags($validated["tags"]);
            $post->attachTags($tags);

            return $post;
        });
    }

    /**
     * Прикрепляет аудиофайл к посту.
     *
     * Если ранее был аудиофайл, он удаляется.
     * Файл сохраняется через AudioFileService.
     *
     * @param Post $post Пост, к которому прикрепляется аудио
     * @param UploadedFile $file Загруженный аудиофайл
     * @return \App\Models\AudioFile Модель аудиофайла
     */
    public function attachAudio(Post $post, UploadedFile $file): AudioFile
    {
        $audio = DB::transaction(function () use ($post, $file): AudioFile {
            $post->audio?->forceDelete();

            $audio = $this->audioFileService
                ->createAudioFileModel($file)
                ->post()
                ->associate($post);
            $audio->save();

            return $audio;
        });

        $this->audioFileService->storeFile($audio, $file);

        event(new AudioAttachedToPostEvent($audio->id));

        return $audio;
    }
}

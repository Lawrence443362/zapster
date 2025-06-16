<?php

namespace App\Http\Resources\V1;

use App\Services\AudioFile\UrlService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     description="Сущность поста с основной информацией, тегами, автором и аудиофайлом",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="My first post"),
 *     @OA\Property(property="description", type="string", nullable=true, example="This is a post description"),
 *     @OA\Property(property="createdAt", type="string", format="date-time", example="2024-06-15 14:30:00"),
 *     @OA\Property(property="authorName", type="string", nullable=true, example="John Doe"),
 *     @OA\Property(property="user_id", type="integer", example=2),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Tag")
 *     ),
 *     @OA\Property(property="soundTrackName", type="string", nullable=true, example="track.mp3"),
 *     @OA\Property(property="soundTrackPath", type="string", nullable=true, example="https://cdn.example.com/audio/track.mp3")
 * )
 *
 * Ресурс для модели Post.
 *
 * @mixin \App\Models\Post
 */
class PostResource extends JsonResource
{
    /**
     * Преобразует ресурс в массив.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->when($request->routeIs('posts.show'), $this->description),
            "createdAt" => $this->created_at->format("Y-m-d H:i:s"),
            "authorName" => $this->whenLoaded("user", fn() => $this->user->name),
            "user_id" => $this->user_id,
            "tags" => $this->whenLoaded("tags", fn() => TagResource::collection($this->tags)),
            "soundTrackName" => $this->whenLoaded("audio", fn() => $this->audio->original_name),
            "soundTrackPath" => $this->whenLoaded("audio", fn() => $this->getAudioUrl()),
        ];
    }

    /**
     * Возвращает URL аудиофайла.
     *
     * @return string
     */
    public function getAudioUrl(): string
    {
        $urlService = App::make(UrlService::class);

        if ($this->audio->is_compressed) {
            return $urlService->getURL($this->audio, true, true);
        } else {
            return $urlService->getURL($this->audio, false, true);
        }
    }
}

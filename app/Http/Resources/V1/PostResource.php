<?php

namespace App\Http\Resources\V1;

use App\Services\AudioFile\UrlService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/**
 * Ресурс для модели Post
 *
 * @mixin \App\Models\Post
 */
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->when(Route::currentRouteName() == 'posts.show', $this->description),
            "createdAt" => Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
            "authorName" => $this->whenLoaded("user", $this->user->name),
            "user_id" => $this->user_id,
            "tags" => $this->whenLoaded("tags", fn() => TagResource::collection($this->tags)),
            "soundTrackName" => $this->whenLoaded("audio", $this->audio->original_name),
            "soundTrackPath" => $this->whenLoaded("audio", $this->getAudioUrl())
        ];
    }

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

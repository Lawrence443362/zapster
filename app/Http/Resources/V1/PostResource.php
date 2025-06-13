<?php

namespace App\Http\Resources\V1;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

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
            "authorName" => $this->whenLoaded("user" , fn() => $this->user->name),
        ];
    }
}

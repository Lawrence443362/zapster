<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Tag",
 *     type="object",
 *     title="Tag",
 *     description="Тег поста",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Technology"),
 *     @OA\Property(property="createAt", type="string", format="date-time", example="2024-06-15 14:30:00")
 * )
 *
 * Ресурс для модели Tag.
 *
 * @mixin \App\Models\Tag
 */
class TagResource extends JsonResource
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
            "id"=> $this->id,
            "name"=> $this->name,
            "createAt"=> Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
        ];
    }
}

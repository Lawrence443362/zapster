<?php

namespace App\Models;

/**
 * Enum PostStatus
 *
 * Статус поста.
 *
 * @method static static Active()
 * @method static static Inactive()
 *
 * @OA\Schema(
 *     schema="PostStatus",
 *     type="string",
 *     description="Статус поста",
 *     enum={"active", "inactive"},
 *     example="active"
 * )
 */
enum PostStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    /**
     * Получить список значений enum.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

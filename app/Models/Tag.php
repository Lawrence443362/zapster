<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Моделька тегов
 *
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   title="User",
 *   required={"id", "name", "email"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Иван Иванов"),
 *   @OA\Property(property="email", type="string", format="email", example="ivan@example.com"),
 *   @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PostTag|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tag extends Model
{
    use HasFactory;
    //

    protected $fillable = [
        "name"
    ];

    /**
     * Создать или найти теги по массиву названий.
     *
     * @param array<int, string> $tags_data
     * @return \Illuminate\Support\Collection<int, Tag>
     */
    public static function createAllNewTags(array $tags_data): Collection
    {
        return collect($tags_data)->map([self::class, 'createOneTag']);
    }

    /**
     * Создать или найти тег по имени.
     *
     * @param string $name
     * @return \App\Models\Tag
     */
    public static function createOneTag(string $name): Tag
    {
        return Tag::firstOrCreate(['name' => $name]);
    }

    /**
     * Мутатор для name — приведение к нижнему регистру и удаление пробелов.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower(trim($value))
        );
    }

    /**
     * Получить посты, связанные с этим тегом.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class)->using(PostTag::class)->withTimestamps();
    }
}

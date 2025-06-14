<?php

namespace App\Models;

use App\QueryFilters\TagFilter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
    //

    protected $fillable = [
        "name"
    ];

    public static function createAllNewTags(array $tags_data): Collection
    {
        $tags = collect($tags_data)
            ->map([self::class, 'createOneTag']);

        return $tags;
    }

    public static function createOneTag(string $name): Tag
    {
        return Tag::firstOrCreate(['name' => $name]);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower(trim($value))
        );
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class)->using(PostTag::class)->withTimestamps();
    }
}

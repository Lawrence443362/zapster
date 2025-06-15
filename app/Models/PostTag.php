<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 
 *
 * @property int $post_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PostTag extends Pivot
{
    public $timestamps = true;
}

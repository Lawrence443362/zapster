<?php

namespace App\Models;

use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 *
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUserId($value)
 * @mixin \Eloquent
 */
class Post extends Model
{
    use SoftDeletes;

    //

    protected $fillable = [
        "title",
        "description",
        "status"
    ];

    protected $casts = [
        "status" => PostStatus::class,
    ];

    public function attachTags(Collection $tags): static
    {
        $this->tags()->sync($tags->pluck("id")->all());

        return $this;
    }

    public function updateTags(Collection $tags): static
    {
        $this->tags()->sync($tags->pluck("id")->all());

        return $this;
    }

    public function storeAudioFile($file)
    {
        $storedName = (string) Str::uuid();
        $folder = 'posts/audio';
        $disk = config('filesystems.default');

        $file->storeAs($folder, $storedName . '.' . $file->extension(), $disk);

        $this->audio()->create([
            'original_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'stored_name' => $storedName,
            'folder' => $folder,
            'disk' => $disk,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->extension(),
            'duration' => null, // можно позже заполнить через ffmpeg
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function audio(): HasOne
    {
        return $this->hasOne(PostAudio::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->using(PostTag::class)->withTimestamps();
    }
}

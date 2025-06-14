<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PostAudio extends Model
{
    use SoftDeletes;

    //

    protected $table = 'post_audios';

    protected $fillable = [
        'post_id',
        'original_name',
        'stored_name',
        'folder',
        'disk',
        'size',
        'mime_type',
        'extension',
        'duration',
    ];

    protected $casts = [
        'size' => 'integer',
        'duration' => 'float',
    ];


    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function path(): string
    {
        return "{$this->folder}/{$this->stored_name}.{$this->extension}";
    }

    public function url(): string
    {
        return Storage::disk(name: $this->disk)->url($this->path());
    }

    /**
     * Удалить файл из хранилища при удалении модели.
     */
    protected static function booted(): void
    {
        static::deleting(function (PostAudio $audio) {
            if (!$audio->isForceDeleting()) {
                return;
            }

            Storage::disk($audio->disk)->delete($audio->path());
        });
    }
}

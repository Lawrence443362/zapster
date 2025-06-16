<?php

namespace App\Models;

use App\Services\AudioFileService;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Модель аудиофайла, связанного с постом.
 * Хранит оригинальные и сжатые версии аудиофайлов, а также информацию о файле.
 *
 * При удалении модели автоматически удаляются связанные файлы с помощью AudioFileService.
 *
 * @property int $id
 * @property int $post_id
 * @property string $original_name
 * @property string $stored_name
 * @property string $folder
 * @property string $disk
 * @property int $size
 * @property string $mime_type
 * @property string $extension
 * @property float|null $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_compressed
 * @property string|null $compressed_disk
 * @property string|null $compressed_folder
 * @property-read \App\Models\Post $post
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereCompressedDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereCompressedFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereIsCompressed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereStoredName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostAudio withoutTrashed()
 * @mixin \Eloquent
 */
class AudioFile extends Model
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
        'compressed_folder',
        'compressed_disk',
        'size',
        'mime_type',
        'extension',
        'duration',
        'is_compressed'
    ];

    protected $casts = [
        'size' => 'integer',
        'duration' => 'float',
    ];

    /**
     * Получить имя файла с его расширением.
     */
    public function getFullStoredName(): string
    {
        return "{$this->stored_name}.{$this->extension}";
    }

    public function compressed($compressed_disk, $compressed_folder)
    {
        $this->is_compressed = true;
        $this->compressed_disk = $compressed_disk;
        $this->compressed_folder = $compressed_folder;

        return $this;
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Удалить файл из хранилища при удалении модели.
     */
    protected static function booted(): void
    {
        static::deleting(function (AudioFile $audioFile) {
            $service = App::make(AudioFileService::class);
            $service->deleteAllFiles($audioFile);
        });
    }
}

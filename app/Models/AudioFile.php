<?php

namespace App\Models;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 *
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
        'disk',
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


    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getAbsolutuFilePath(): string
    {
        return Storage::disk(name: $this->disk)->path($this->getRelativePath());
    }

    public function getRelativeCompressedFilePath(): string
    {
        return "{$this->compressed_folder}/{$this->stored_name}.{$this->extension}";
    }

    public function getAbsoluteCompressedFilePath(): string
    {
        return Storage::disk($this->disk)->path($this->getRelativePath());
    }

    public function getUrl(): string
    {
        if ($this->is_compressed) {
            return $this->getCompressedFileUrl();
        } else {
            return $this->getFileUrl();
        }
    }

    public function getFileUrl(): string
    {
        return Storage::disk(name: $this->disk)->getUrl($this->getRelativePath());
    }

    public function getCompressedFileUrl(): string
    {
        return Storage::disk(name: $this->disk)->getUrl($this->getRelativePath());
    }

    public function generateRelativeCompressedFilePath(): string
    {
        $uuid = Str::uuid()->toString();
        return "posts/audio_compressed/{$this->stored_name}.{$this->extension}";
    }

    public function generateAbsoluteCompressedFilePath($disk): string
    {
        return Storage::disk($disk)->path($this->generateRelativeCompressedFilePath());
    }

    public function compressMp3(): string
    {
        $inputPath = $this->getAbsolutuFilePath();
        $outputPath = $this->generateAbsoluteCompressedFilePath(config('filesystems.default'));
        $command = "ffmpeg -y -i \"$inputPath\" -c:a libmp3lame -b:a 128k \"$outputPath\" 2>&1";

        exec($command, $outputLog, $returnCode);

        if ($returnCode !== 0) {
            // Лог ошибки если ffmpeg сломался
            Log::error("FFmpeg error:", $outputLog);
            throw new \Exception("FFmpeg failed. Check logs.");
        }

        $this->update([
            'is_compressed' => true,
            'size' => Storage::disk($this->disk)->size(path: $outputPath)
        ]);

        return $this;
    }

    /**
     * Удалить файл из хранилища при удалении модели.
     */
    protected static function booted(): void
    {
        static::deleting(function (AudioFile $audio) {
            if (!$audio->isForceDeleting()) {
                return;
            }

            Storage::disk($audio->disk)->delete($audio->getRelativeFilePath());

            if ($audio->is_compressed) {
                Storage::disk($audio->compressed_disk)->delete($audio->getRelativeCompressedFilePath());
            }
        });
    }
}

<?php

namespace App\Models;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function path(): string
    {
        return "{$this->folder}/{$this->stored_name}.{$this->extension}";
    }

    public function generateCompressedPath(): string
    {
        $uuid = Str::uuid()->toString();
        return "posts/audio_compressed/{$this->stored_name}.{$this->extension}";
    }

    public function url(): string
    {
        return Storage::disk(name: $this->disk)->url($this->path());
    }

    public function compressMp3(): string
    {
        $originalFullPath = Storage::disk($this->disk)->path($this->path());
        $tempPath = Storage::disk($this->disk)->path($this->generateCompressedPath());


        $command = "ffmpeg -y -i \"$originalFullPath\" -c:a libmp3lame -b:a 128k \"$tempPath\" 2>&1";
        exec($command, $outputLog, $returnCode);

        if ($returnCode !== 0) {
            // Лог ошибки если ffmpeg сломался
            Log::error("FFmpeg error:", $outputLog);
            throw new \Exception("FFmpeg failed. Check logs.");
        }

        $this->update([
            'is_compressed' => true,
            'size' => Storage::disk($this->disk)->size(path: $tempPath)
        ]);

        return $this;
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

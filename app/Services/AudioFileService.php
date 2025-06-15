<?php

namespace App\Services;

use App\Models\AudioFile;

class AudioFileService
{
        public function getPath(AudioFile $audioFile, bool $isCompressedFile = false, bool $fullPath = false): string
    {
        $relativePath = $this->getRelativePath($audioFile, $isCompressedFile);

        if ($fullPath) {
            $disk = $this->getDisc($audioFile, $isCompressedFile);

            return Storage::disk($disk)->path($relativePath);
        } else {
            return $relativePath;
        }
    }
    public function getRelativePath(AudioFile $audio): string
    {
        return "{$audio->folder}/{$audio->stored_name}.{$audio->extension}";
    }

    // public function getAbsolutuPath(PostAudio $audio): string
    // {
    //     return Storage::disk($this->disk)->path($this->getRelativePath());
    // }

    // public function getRelativeCompressedPath(PostAudio $audio): string
    // {
    //     return "{$this->compressed_folder}/{$this->stored_name}.{$this->extension}";
    // }

    // public function getAbsoluteCompressedPath(PostAudio $audio): string
    // {
    //     return Storage::disk($this->disk)->path($this->getRelativePath());
    // }

    // public function generateRelativeCompressedPath(PostAudio $audio): string
    // {
    //     $uuid = Str::uuid()->toString();
    //     return "posts/audio_compressed/{$this->stored_name}.{$this->extension}";
    // }

    // public function generateAbsoluteCompressedPath(PostAudio $audio, $disk): string
    // {
    //     return Storage::disk($disk)->path($this->generateRelativeCompressedPath());
    // }
}

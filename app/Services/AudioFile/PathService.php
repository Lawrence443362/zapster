<?php

namespace App\Services\AudioFile;

use App\Models\AudioFile;
use Illuminate\Support\Facades\Storage;

class PathService
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

    public function getRelativePath(AudioFile $audioFile, bool $isCompressedFile = false): string
    {
        $folder = $this->getFolder($audioFile, $isCompressedFile);
        $relativePath = "/{$folder}/{$audioFile->stored_name}.{$audioFile->extension}";

        return $relativePath;
    }

    public function getDisc(AudioFile $audioFile, bool $forCompressedFile): string|null
    {
        if (!$forCompressedFile) {
            return $audioFile->disk;
        }

        if (!$audioFile->is_compressed) {
            throw new \Exception("You can't get disc for compressed audio file, because audio file is not compressed.");
        }

        return $audioFile->compressed_disk;
    }

    public function getFolder(AudioFile $audioFile, bool $forCompressedFile): string|null
    {
        if (!$forCompressedFile) {
            return $audioFile->folder;
        }

        if (!$audioFile->is_compressed) {
            throw new \Exception("You can't get folder for compressed audio file, because audio file is not compressed.");
        }

        return $audioFile->compressed_folder;
    }
}

<?php

namespace App\Services\AudioFile;

use App\Models\AudioFile;
use Illuminate\Contracts\Filesystem\Factory as StorageFactory;

class PathGeneratorService
{
    private StorageFactory $storage;
    protected string $folder;
    protected string $disk;
    protected string $compressedDisk;
    protected string $compressedFolder;

    public function __construct(StorageFactory $storage, string $folder, string $disk, string $compressedDisk, string $compressedFolder)
    {
        $this->storage = $storage;
        $this->disk = $disk;
        $this->folder = $folder;
        $this->compressedDisk = $compressedDisk;
        $this->compressedFolder = $compressedFolder;
    }

    public function generatePath(AudioFile $audioFile, bool $forCompressedFile = false, bool $fullPath = false): string
    {
        $relativePath = $this->generateRelativePath($audioFile, $forCompressedFile);

        if ($fullPath) {
            $disk = $this->getDisc($forCompressedFile);

            return $this->storage->disk($disk)->path($relativePath);
        } else {
            return $relativePath;
        }
    }

    public function generateRelativePath(AudioFile $audioFile, bool $forCompressedFile = false): string
    {
        $folder = $this->getFolder($forCompressedFile);
        $relativePath = "/{$folder}/{$audioFile->stored_name}.{$audioFile->extension}";

        return $relativePath;
    }

    public function getDisc(bool $forCompressedFile): string
    {
        if ($forCompressedFile) {
            return $this->compressedDisk;
        } else {
            return $this->disk;
        }
    }

    public function getFolder(bool $forCompressedFile): string
    {
        if ($forCompressedFile) {
            return $this->compressedFolder;
        } else {
            return $this->folder;
        }
    }
}

<?php

namespace App\Services;

use App\Contracts\UuidGeneratorInterface;
use App\Factories\AudioFileFactory;
use App\Models\AudioFile;
use App\Services\AudioFile\PathGeneratorService;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Filesystem\Factory as StorageFactory;

class AudioFileService
{
    public PathGeneratorService $pathGeneratorService;
    public UuidGeneratorInterface $uuidGenerator;
    public AudioFileFactory $audioFileFactory;
    public StorageFactory $storageFactory;

    public function __construct(
        PathGeneratorService $pathGeneratorService,
        UuidGeneratorInterface $uuidGenerator,
        AudioFileFactory $audioFileFactory,
        StorageFactory $storageFactory
    ) {
        $this->pathGeneratorService = $pathGeneratorService;
        $this->uuidGenerator = $uuidGenerator;
        $this->audioFileFactory = $audioFileFactory;
        $this->storageFactory = $storageFactory;
    }

    public function createAudioFileModel(UploadedFile $file): AudioFile
    {
        return $this->audioFileFactory->fromUploadedFile($file);
    }

    public function storeFile(AudioFile $audioFile, UploadedFile $file): void
    {
        $this->storageFactory
            ->disk($audioFile->disk)
            ->putFileAs($audioFile->folder, $file, $audioFile->getFullStoredName());
    }
}

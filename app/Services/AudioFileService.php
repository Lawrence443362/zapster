<?php

namespace App\Services;

use App\Contracts\UuidGeneratorInterface;
use App\Models\AudioFile;
use App\Models\Post;
use App\Services\AudioFile\AudioFileCompressorService;
use App\Services\AudioFile\PathGeneratorService;
use App\Services\AudioFile\PathService;
use Illuminate\Http\UploadedFile;

class AudioFileService
{
    public PathGeneratorService $pathGeneratorService;
    public UuidGeneratorInterface $uuidGenerator;
    public function __construct(PathGeneratorService $pathGeneratorService, UuidGeneratorInterface $uuidGenerator)
    {
        $this->pathGeneratorService = $pathGeneratorService;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function saveAudioFile(UploadedFile $file, Post $post)
    {
        $disk = $this->pathGeneratorService->getDisc();
        $folder = $this->pathGeneratorService->getFolder();
        $fileName = $this->uuidGenerator->generate();

        $file->storeAs($folder, "{$fileName}.{$file->extension()}", $disk);

        return $post->audio()->create([
            'original_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'stored_name' => $fileName, 
            'folder' => $folder,
            'disk' => $disk,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->extension(),
            'duration' => null, // можно позже заполнить через ffmpeg
        ]);
    }
}

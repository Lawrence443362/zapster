<?php

namespace App\Services;

use App\Models\AudioFile;
use App\Services\AudioFile\FFMpegRunner;
use App\Services\AudioFile\PathGeneratorService;
use App\Services\AudioFile\PathService;
use Illuminate\Support\Facades\Log;


class FileCompressorService
{
    public PathService $pathService;
    public PathGeneratorService $pathGeneratorService;
    public FFMpegRunner $ffmPegRunner;

    public function __construct(PathService $pathService, PathGeneratorService $pathGeneratorService, FFMpegRunner $ffmPegRunner)
    {
        $this->pathService = $pathService;
        $this->pathGeneratorService = $pathGeneratorService;
        $this->ffmPegRunner = $ffmPegRunner;
    }

    public function compress(AudioFile $audioFile)
    {
        $source = $this->pathService->getPath(audioFile: $audioFile, forCompressedFile: false, fullPath: true);
        $output = $this->pathGeneratorService->generatePath(audioFile: $audioFile, forCompressedFile: true, fullPath: true);

        $command = "ffmpeg -y -i \"$source\" -c:a libmp3lame -b:a 128k \"$output\" 2>&1";

        exec($command, $outputLog, $returnCode);

        if ($returnCode !== 0) {
            // Лог ошибки если ffmpeg сломался
            Log::error("FFmpeg error:", $outputLog);
            throw new \Exception("FFmpeg failed. Check logs.");
        }
    }

}

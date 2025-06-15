<?php

namespace App\Services\AudioFile;

use App\Models\AudioFile;
use App\Services\AudioFile\FFMpegRunner;
use App\Services\AudioFile\PathGeneratorService;
use App\Services\AudioFile\PathService;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для сжатия аудиофайлов с помощью FFMpeg.
 *
 * Получает пути к исходному и выходному файлу, затем вызывает процесс сжатия.
 */
class AudioFileCompressorService
{
    /**
     * @var PathService Сервис для получения полного пути к файлу.
     */
    public PathService $pathService;

    /**
     * @var PathGeneratorService Сервис для генерации относительных и полных путей.
     */
    public PathGeneratorService $pathGeneratorService;

    /**
     * @var FFMpegRunner Обёртка над вызовом ffmpeg через Symfony Process.
     */
    public FFMpegRunner $ffmPegRunner;

    /**
     * Конструктор сервиса сжатия файлов.
     *
     * @param PathService $pathService Сервис получения путей.
     * @param PathGeneratorService $pathGeneratorService Сервис генерации путей.
     * @param FFMpegRunner $ffmPegRunner Обёртка над ffmpeg.
     */
    public function __construct(
        PathService $pathService,
        PathGeneratorService $pathGeneratorService,
        FFMpegRunner $ffmPegRunner
    ) {
        $this->pathService = $pathService;
        $this->pathGeneratorService = $pathGeneratorService;
        $this->ffmPegRunner = $ffmPegRunner;
    }

    /**
     * Выполняет сжатие аудиофайла.
     *
     * @param AudioFile $audioFile Экземпляр модели AudioFile, для которого нужно выполнить сжатие.
     *
     * @throws \Exception Если ffmpeg завершился с ошибкой.
     */
    public function compress(AudioFile $audioFile): void
    {
        $source = $this->pathService->getPath($audioFile, false, true);
        $output = $this->pathGeneratorService->generatePath($audioFile, true, true);

        try {
            $this->ffmPegRunner->run($source, $output);
        } catch (ProcessFailedException $e) {
            Log::error("FFmpeg failed for file [{$audioFile->id}]: " . $e->getProcess()->getErrorOutput());
            throw new \RuntimeException("FFmpeg failed. Check logs.");
        }
    }
}


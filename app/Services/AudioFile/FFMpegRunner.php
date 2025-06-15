<?php

namespace App\Services\AudioFile;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Обёртка над вызовом команды ffmpeg для сжатия аудиофайлов.
 *
 * Класс использует Symfony Process для запуска команды ffmpeg с заданными параметрами.
 * Предназначен для преобразования аудиофайлов в формат MP3 с битрейтом 128 кбит/с.
 */
class FFMpegRunner
{
    /**
     * Запускает ffmpeg для сжатия аудиофайла.
     *
     * @param string $inputPath Путь к исходному аудиофайлу.
     * @param string $outputPath Путь, по которому сохранить сжатый файл.
     *
     * @throws ProcessFailedException Если выполнение команды завершилось с ошибкой.
     */
    public function run(string $inputPath, string $outputPath): void
    {
        $process = new Process([
            'ffmpeg',
            '-y',
            '-i', $inputPath,
            '-c:a', 'libmp3lame',
            '-b:a', '128k',
            $outputPath,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}

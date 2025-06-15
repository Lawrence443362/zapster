<?php

namespace App\Services\AudioFile;

use App\Models\AudioFile;
use Illuminate\Contracts\Filesystem\Factory as StorageFactory;

/**
 * Сервис генерации путей к аудиофайлам.
 * Используется для построения относительных и абсолютных путей к оригинальным и сжатым аудиофайлам.
 */
class PathGeneratorService
{
    public StorageFactory $storage;
    protected string $folder;
    protected string $disk;
    protected string $compressedDisk;
    protected string $compressedFolder;

    /**
     * @param StorageFactory $storage Фабрика для работы с файловыми дисками.
     * @param string $folder Папка хранения оригинальных файлов.
     * @param string $disk Диск хранения оригинальных файлов.
     * @param string $compressedDisk Диск хранения сжатых файлов.
     * @param string $compressedFolder Папка хранения сжатых файлов.
     */
    public function __construct(StorageFactory $storage, string $folder, string $disk, string $compressedDisk, string $compressedFolder)
    {
        $this->storage = $storage;
        $this->disk = $disk;
        $this->folder = $folder;
        $this->compressedDisk = $compressedDisk;
        $this->compressedFolder = $compressedFolder;
    }

    /**
     * Генерирует путь к аудиофайлу.
     *
     * @param AudioFile $audioFile Модель аудиофайла.
     * @param bool $forCompressedFile Указывает, генерируется ли путь для сжатого файла.
     * @param bool $fullPath Указывает, нужно ли вернуть абсолютный путь.
     * @return string Относительный или абсолютный путь к файлу.
     */
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

    /**
     * Генерирует относительный путь к файлу.
     *
     * @param AudioFile $audioFile Модель аудиофайла.
     * @param bool $forCompressedFile Указывает, генерируется ли путь для сжатого файла.
     * @return string Относительный путь вида /folder/filename.ext
     */
    public function generateRelativePath(AudioFile $audioFile, bool $forCompressedFile = false): string
    {
        $folder = $this->getFolder($forCompressedFile);
        $relativePath = "/{$folder}/{$audioFile->stored_name}.{$audioFile->extension}";

        return $relativePath;
    }

    /**
     * Возвращает имя диска в зависимости от типа файла.
     *
     * @param bool $forCompressedFile Указывает, нужен ли диск для сжатого файла.
     * @return string Имя диска.
     */
    public function getDisc(bool $forCompressedFile = false): string
    {
        if ($forCompressedFile) {
            return $this->compressedDisk;
        } else {
            return $this->disk;
        }
    }

    /**
     * Возвращает имя папки в зависимости от типа файла.
     *
     * @param bool $forCompressedFile Указывает, нужна ли папка для сжатого файла.
     * @return string Имя папки.
     */
    public function getFolder(bool $forCompressedFile = false): string
    {
        if ($forCompressedFile) {
            return $this->compressedFolder;
        } else {
            return $this->folder;
        }
    }
}

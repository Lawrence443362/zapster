<?php

namespace App\Services\AudioFile;

use App\Models\AudioFile;
use Illuminate\Contracts\Filesystem\Factory as StorageFactory;
use Illuminate\Support\Facades\Storage;

/**
 * Сервис для работы с URLами к аудиофайлам (оригинальным и сжатым).
 */
class UrlService
{
    /**
     * Фабрика для получения дисков хранения файлов.
     *
     * @var StorageFactory
     */
    private StorageFactory $storage;

    /**
     * Конструктор.
     *
     * @param StorageFactory $storage Фабрика для работы с файловой системой.
     */
    public function __construct(StorageFactory $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Возвращает URL к аудиофайлу (относительный или абсолютный).
     *
     * @param AudioFile $audioFile Экземпляр аудиофайла.
     * @param bool $forCompressedFile Указывает, нужно ли получить путь к сжатому файлу.
     * @param bool $fullPath Указывает, нужно ли вернуть абсолютный путь в файловой системе.
     * @return string Путь к файлу.
     *
     * @throws \Exception Если указан сжатый файл, но он не существует.
     */
    public function getURL(AudioFile $audioFile, bool $forCompressedFile = false, bool $fullPath = false): string
    {
        $relativePath = $this->getRelativeURL($audioFile, $forCompressedFile);

        if ($fullPath) {
            $disk = $this->getDisc($audioFile, $forCompressedFile);

            return $this->storage->disk($disk)->url($relativePath);
        } else {
            return $relativePath;
        }
    }

    /**
     * Возвращает относительный URL.
     *
     * @param AudioFile $audioFile Экземпляр аудиофайла.
     * @param bool $forCompressedFile Указывает, нужно ли получить путь к сжатому файлу.
     * @return string Относительный путь.
     *
     * @throws \Exception Если файл не сжат, но запрошен путь к сжатой версии.
     */
    public function getRelativeURL(AudioFile $audioFile, bool $forCompressedFile = false): string
    {
        $folder = $this->getFolder($audioFile, $forCompressedFile);
        $relativePath = "/{$folder}/{$audioFile->stored_name}.{$audioFile->extension}";

        return $relativePath;
    }

    /**
     * Возвращает диск хранения файла.
     *
     * @param AudioFile $audioFile Экземпляр аудиофайла.
     * @param bool $forCompressedFile Указывает, нужно ли получить диск для сжатого файла.
     * @return string|null Название диска.
     *
     * @throws \Exception Если файл не сжат, но запрошен диск для сжатой версии.
     */
    public function getDisc(AudioFile $audioFile, bool $forCompressedFile = false): string|null
    {
        if (!$forCompressedFile) {
            return $audioFile->disk;
        }

        if (!$audioFile->is_compressed) {
            throw new \Exception("You can't get disc for compressed audio file, because audio file is not compressed.");
        }

        return $audioFile->compressed_disk;
    }

    /**
     * Возвращает папку хранения файла.
     *
     * @param AudioFile $audioFile Экземпляр аудиофайла.
     * @param bool $forCompressedFile Указывает, нужно ли получить папку для сжатого файла.
     * @return string|null Название папки.
     *
     * @throws \Exception Если файл не сжат, но запрошена папка для сжатой версии.
     */
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

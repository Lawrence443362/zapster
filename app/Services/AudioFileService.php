<?php

namespace App\Services;

use App\Contracts\UuidGeneratorInterface;
use App\Factories\AudioFileFactory;
use App\Models\AudioFile;
use App\Services\AudioFile\PathGeneratorService;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Filesystem\Factory as StorageFactory;

/**
 * Сервис для работы с аудиофайлами: создание модели и сохранение файла.
 */
class AudioFileService
{
    /**
     * @var PathGeneratorService Сервис генерации путей для хранения файлов.
     */
    public PathGeneratorService $pathGeneratorService;

    /**
     * @var UuidGeneratorInterface Интерфейс генерации UUID.
     */
    public UuidGeneratorInterface $uuidGenerator;

    /**
     * @var AudioFileFactory Фабрика создания моделей AudioFile на основе загруженных файлов.
     */
    public AudioFileFactory $audioFileFactory;

    /**
     * @var StorageFactory Фабрика для работы с файловыми дисками.
     */
    public StorageFactory $storageFactory;

    /**
     * Конструктор сервиса аудиофайлов.
     *
     * @param PathGeneratorService $pathGeneratorService Сервис генерации путей.
     * @param UuidGeneratorInterface $uuidGenerator Интерфейс генерации UUID.
     * @param AudioFileFactory $audioFileFactory Фабрика создания моделей AudioFile.
     * @param StorageFactory $storageFactory Фабрика для работы с файловыми системами.
     */
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

    /**
     * Создаёт модель AudioFile на основе загруженного файла.
     *
     * @param UploadedFile $file Загруженный пользователем файл.
     * @return AudioFile Несохранённая модель аудиофайла.
     */
    public function createAudioFileModel(UploadedFile $file): AudioFile
    {
        return $this->audioFileFactory->fromUploadedFile($file);
    }

    /**
     * Сохраняет физический файл в файловую систему.
     *
     * @param AudioFile $audioFile Модель с параметрами пути, имени и диска.
     * @param UploadedFile $file Загруженный файл, который нужно сохранить.
     * @return void
     */
    public function storeFile(AudioFile $audioFile, UploadedFile $file): void
    {
        $this->storageFactory
            ->disk($audioFile->disk)
            ->putFileAs($audioFile->folder, $file, $audioFile->getFullStoredName());
    }
}

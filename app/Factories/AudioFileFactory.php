<?php

namespace App\Factories;

use App\Contracts\UuidGeneratorInterface;
use App\Models\AudioFile;
use App\Services\AudioFile\PathGeneratorService;
use Illuminate\Http\UploadedFile;

/**
 * Фабрика для создания экземпляров AudioFile из загруженного файла.
 *
 * По дефолту выставляем свойства связанные с compressed - пустыми,
 * когда файлик ужметься, то поля заполняться.
 * 
 */
class AudioFileFactory
{
    public function __construct(
        private PathGeneratorService $pathGeneratorService,
        private UuidGeneratorInterface $uuidGenerator
    ) {
    }

    /**
     * Создаёт модель AudioFile из загруженного файла.
     *
     * @param UploadedFile $file Загруженный пользователем аудиофайл
     * @return AudioFile Не сохранённая модель AudioFile
     */
    public function fromUploadedFile(UploadedFile $file): AudioFile
    {
        $uuid = $this->uuidGenerator->generate();
        $folder = $this->pathGeneratorService->getFolder();
        $disk = $this->pathGeneratorService->getDisc();

        return new AudioFile([
            'original_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'stored_name' => $uuid,
            'folder' => $folder,
            'disk' => $disk,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->extension(),
            'duration' => null,
        ]);
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AudioFile\PathGeneratorService;
use Illuminate\Contracts\Filesystem\Factory as StorageFactory;

/**
 * Провайдер сервиса генерации путей для аудиофайлов.
 *
 * Регистрирует в контейнере singleton-сервис PathGeneratorService,
 * который отвечает за генерацию путей для хранения аудиофайлов и их сжатых версий.
 */
class PathGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Регистрирует сервисы в контейнере приложения.
     *
     * Создаёт singleton PathGeneratorService с параметрами из конфигурации:
     * - StorageFactory (файловая система)
     * - Папка для аудиофайлов
     * - Диск для аудиофайлов
     * - Диск для сжатых аудиофайлов
     * - Папка для сжатых аудиофайлов
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(PathGeneratorService::class, function ($app) {
            $storage = $app->make(StorageFactory::class);

            return new PathGeneratorService(
                $storage,
                config('audio.folder'),
                config('audio.disk'),
                config('audio.compressed_disk'),
                config('audio.compressed_folder'),
            );
        });
    }

    /**
     * Метод для запуска логики после регистрации сервисов.
     *
     * В данном провайдере не используется.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AudioFile\PathGeneratorService;
use Illuminate\Contracts\Filesystem\Factory as StorageFactory;

class PathGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
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
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

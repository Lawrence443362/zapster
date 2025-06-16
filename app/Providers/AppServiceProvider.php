<?php

namespace App\Providers;

use App\Contracts\UuidGeneratorInterface;
use App\Services\UuidGeneratorService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * Основной сервис-провайдер приложения.
 *
 * Регистрирует сервисы и настраивает лимитирование запросов API.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Регистрирует привязки классов и сервисов в контейнере приложения.
     *
     * В данном случае связывает интерфейс генератора UUID с конкретной реализацией.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(UuidGeneratorInterface::class, UuidGeneratorService::class);
    }

    /**
     * Выполняет загрузку и инициализацию сервисов после регистрации.
     *
     * Настраивает лимитирование запросов API:
     * - Максимум 60 запросов в минуту на одного пользователя (по ID) или по IP.
     * - При превышении лимита возвращает JSON-ответ с ошибкой 429.
     *
     * @return void
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too Many Attempts.'
                    ], 429);
                });
        });
    }
}

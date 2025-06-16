<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Contracts\UuidGeneratorInterface;

/**
 * Сервис генерации UUID.
 *
 * Реализация интерфейса UuidGeneratorInterface.
 * Использует встроенный в Laravel класс Str для генерации UUID версии 4.
 */
class UuidGeneratorService implements UuidGeneratorInterface
{
    /**
     * Генерирует новый UUID (v4) и возвращает его строковое представление.
     *
     * @return string Сгенерированный UUID
     */
    public function generate(): string
    {
        return (string) Str::uuid();
    }
}

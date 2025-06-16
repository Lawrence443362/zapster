<?php

namespace App\Contracts;

/**
 * Контракт для генерации UUID.
 */
interface UuidGeneratorInterface
{
    /**
     * Генерирует UUID в виде строки.
     *
     * @return string Уникальный идентификатор
     */
    public function generate(): string;
}

<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Contracts\UuidGeneratorInterface;

class UuidGeneratorService implements UuidGeneratorInterface
{
    public function generate(): string
    {
        return (string) Str::uuid();
    }
}

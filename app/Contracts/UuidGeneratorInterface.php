<?php

namespace App\Contracts;

interface UuidGeneratorInterface
{
    public function generate(): string;
}

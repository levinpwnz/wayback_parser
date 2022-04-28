<?php

declare(strict_types=1);

namespace App\Contracts;

interface WriterContract
{
    public function write(string $line): void;
}
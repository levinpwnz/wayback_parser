<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Dto\WayBackResponseDto;

interface ParserContract
{
    public function parse(string $url): ?WayBackResponseDto;
}
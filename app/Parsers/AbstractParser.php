<?php

declare(strict_types=1);

namespace App\Parsers;

use App\Contracts\ParserContract;
use GuzzleHttp\Client;

abstract class AbstractParser implements ParserContract
{
    protected function getClient(bool $verify): Client
    {
        return (new Client([
            'verify' => $verify
        ]));
    }
}
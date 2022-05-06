<?php

declare(strict_types=1);

namespace App\Parsers;

use App\Contracts\ParserContract;
use GuzzleHttp\Client;

abstract class AbstractParser implements ParserContract
{
    protected function getClient(bool $verify = false): Client
    {
        return (new Client([
            'verify' => $verify
        ]));
    }

    protected function extractTitle(?string $url): ?string
    {
        if (is_null($url)) {
            return 'Not found';
        }

        $body = $this
            ->getClient()
            ->get($url)
            ->getBody()
            ->getContents();

        $possibleTitle = preg_match("/<title>(.*)<\/title>/siU", $body, $title);

        unset($body);

        if (!$possibleTitle) {
            return null;
        }

        $title = preg_replace('/\s+/', ' ', $title[1]);

        return trim($title);
    }
}
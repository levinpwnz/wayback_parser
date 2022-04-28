<?php

declare(strict_types=1);

namespace App\Parsers;

use App\Contracts\ParserContract;
use App\Dto\WayBackResponseDto;
use App\Log\LogWriter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WaybackParser implements ParserContract
{
    private string $baseUrl = 'http://archive.org/wayback/available?';

    public function parse(string $url): WayBackResponseDto
    {
        $dto = (new WayBackResponseDto());

        try {
            $response = $this->getClient()
                ->get($this->baseUrl . http_build_query(['url' => $url]))
                ->getBody()
                ->getContents();

            $response = (object) json_decode($response);

            $dto->setUrl($response?->url)
                ->setSnapshotYear(
                    $response
                        ?->archived_snapshots
                        ?->closest
                        ?->timestamp
                        ? (int)date('Y', strtotime($response->archived_snapshots->closest->timestamp))
                        : null)
                ->setSnapshotUrl($response?->archived_snapshots?->closest?->url);

        } catch (GuzzleException $exception) {
            (new LogWriter())
                ->write(sprintf('Wayback unavailable. Reason: %s', $exception->getMessage()));
        }

        try {
            $dto->setTitle($this->extractTitle($url));
        } catch (GuzzleException $exception) {
            (new LogWriter())
                ->write(sprintf('Тайтл сайта %s не получили по причине: %s', $url, $exception->getMessage()));
        }

        return $dto;
    }

    private function extractTitle(string $url): ?string
    {
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

    private function getClient(): Client
    {
        return (new Client([
            'verify' => false
        ]));
    }
}
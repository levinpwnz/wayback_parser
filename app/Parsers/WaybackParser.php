<?php

namespace App\Parsers;

use App\Dto\WayBackResponseDto;
use App\Log\LogWriter;
use GuzzleHttp\Exception\GuzzleException;

class WaybackParser extends AbstractParser
{
    private string $baseUrl = 'http://archive.org/wayback/available?';

    public function __construct(private string $timestamp) {}

    public function parse(string $url): ?WayBackResponseDto
    {
        try {
            $dto = (new WayBackResponseDto());

            $response = $this->getClient(false)
                ->get($this->baseUrl . http_build_query([
                        'url' => $url,
                        'timestamp' => $this->timestamp
                    ]))
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
                ->setSnapshotUrl($response?->archived_snapshots?->closest?->url)
                ->setTitle($this->extractTitle($dto->getSnapshotUrl() ?? 'Not found'));

            return $dto;
        } catch (GuzzleException $exception) {
            (new LogWriter())
                ->write(sprintf('Wayback machine for url: %s, says: %s', $url, $exception->getMessage()));
        }

        return null;
    }

    private function extractTitle(string $url): ?string
    {
        $body = $this
            ->getClient(false)
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
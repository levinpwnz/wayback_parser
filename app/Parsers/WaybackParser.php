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

            $response = $this->getClient()
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
                ->setTitle($this->extractTitle($dto->getSnapshotUrl()));

            return $dto;
        } catch (GuzzleException $exception) {
            (new LogWriter())
                ->write(sprintf('Wayback machine for url: %s, says: %s', $url, $exception->getMessage()));
        }

        return null;
    }
}
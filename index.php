<?php

require 'vendor/autoload.php';

ini_set('MEMORY_LIMIT', -1);
ini_set('MAX_EXECUTION_TIME', -1);

use App\Parsers\WaybackParser;

$urls = explode("\n", file_get_contents('domains.txt'));

$fileHandle = fopen('data.csv', 'wb+');

$timestamps = generateTimestamps(1990, 2022, 12);

function generateTimestamps(int $startYear, int $stopYear, int $monthlyStep = 3): array
{
    $timestamps = [];

    $years = range($startYear, $stopYear);
    $months = range(1, 12);

    $startOfMonth = '01';

    foreach ($years as $year) {
        foreach ($months as $month) {
            if ($month % $monthlyStep === 0) {
                $timestamps[] = $year . $month . $startOfMonth;
            }
        }
    }

    return $timestamps;
}


foreach ($urls as $url) {
    $lines = [];

    foreach ($timestamps as $timestamp) {

        $parser = new WaybackParser($timestamp);

        info($timestamp, $url);

        $dto = $parser->parse($url);

        if (is_null($dto)) {
            continue;
        }

        $line = [
            $dto->getUrl(),
            $dto->getTitle(),
            $dto->getSnapshotUrl(),
            $dto->getSnapshotYear()
        ];

        if (in_array($line, $lines)) {
            continue;
        }

        $lines[] = array_map('trim', $line);


        unset($parser);
    }

    foreach ($lines as $line) {
        fputcsv($fileHandle, $line, ';');
    }

    unset($lines);
}


function info(string $timestamp, string $site): void
{
    echo sprintf("CURRENT TS: %s, FOR SITE: %s \r\n", $timestamp, $site);
}

fclose($fileHandle);
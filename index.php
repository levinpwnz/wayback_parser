<?php

require 'vendor/autoload.php';

ini_set('MEMORY_LIMIT', -1);
ini_set('MAX_EXECUTION_TIME', -1);

use App\Parsers\WaybackParser;

$urls = explode("\n", file_get_contents('domains.txt'));

$fileHandle = fopen('data.csv', 'w+');

$parser = new WaybackParser();

foreach ($urls as $url) {
    $dto = $parser->parse($url);

    $line = [
        $dto->getUrl(),
        $dto->getTitle(),
        $dto->getSnapshotUrl(),
        $dto->getSnapshotYear()
    ];

    $line = array_map('trim', $line);

    fputcsv($fileHandle, $line, ';');
}

fclose($fileHandle);
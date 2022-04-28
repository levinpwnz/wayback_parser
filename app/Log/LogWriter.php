<?php

declare(strict_types=1);

namespace App\Log;

use App\Contracts\WriterContract;

class LogWriter implements WriterContract
{
    private mixed $handle;

    public function write(mixed $line): void
    {
        fwrite($this->handle(), $line);
    }

    /**
     * @return resource
     */
    private function handle()
    {
        return $this->handle = fopen($this->logName(), 'a');
    }

    private function logName(): string
    {
        return 'log-' . date('Y-m-d') . '.log';
    }

    public function __destruct()
    {
        fclose($this->handle);
    }
}
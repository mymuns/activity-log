<?php

namespace ActivityLog\Writers;

use Illuminate\Support\Facades\Log;

class LaravelLoggerWriter implements LogWriterInterface
{
    protected string $channel;

    public function __construct(string $channel = 'daily')
    {
        $this->channel = $channel;
    }

    public function write(array $data): void
    {
        Log::channel($this->channel)->info('Activity Log', $data);
    }
}


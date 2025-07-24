<?php

namespace ActivityLog\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use ActivityLog\Writers\LogWriterInterface;
use ActivityLog\Writers\DatabaseWriter;
use ActivityLog\Writers\LaravelLoggerWriter;

class ActivityLoggerService
{
    protected LogWriterInterface $writer;

    public function __construct()
    {
        $channel = config('activitylog.log_channel', 'database');

        $this->writer = match ($channel) {
            'database' => new DatabaseWriter(),
            default    => new LaravelLoggerWriter($channel),
        };
    }

    public function logRequestResponse(Request $request, $response): void
    {
        $requestBody = $request->except(config('activitylog.mask_request_keys', []));
        $responseBody = method_exists($response, 'getContent') ? $response->getContent() : null;

        $this->writer->write([
            'type'           => 'http',
            'method'         => $request->method(),
            'path'           => $request->path(),
            'request_body'   => json_encode($requestBody),
            'response_body'  => mb_strimwidth($responseBody, 0, config('activitylog.max_body_length', 2000)),
            'status_code'    => $response->status(),
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'user_id'        => optional($request->user())->id,
        ]);
    }

    public function logModelEvent(array $data): void
    {
        $this->writer->write([
            'type' => 'model',
            ...$data
        ]);
    }
}


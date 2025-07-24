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

        if ($responseBody !== null) {
            $responseData = json_decode($responseBody, true);

            if (is_array($responseData)) {
                $hideFields = config('activitylog.hide_response_fields', []);

                $this->sanitize($responseData, $hideFields);

                // Tekrar json_encode edip loglayabilirsin
                $safeResponseBody = json_encode($responseData);
            } else {
                // JSON değilse ham haliyle veya başka işlem
                $safeResponseBody = $responseBody;
            }
        } else {
            $safeResponseBody = null;
        }

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

    public function sanitize(array &$data, array $hideFields)
    {
        foreach ($data as $key => &$value) {
            if (in_array($key, $hideFields)) {
                $value = '***';
            } elseif (is_array($value)) {
                $this->sanitize($value, $hideFields);
            }
        }
    }

    public function logModelEvent(array $data): void
    {
        $this->writer->write([
            'type' => 'model',
            ...$data
        ]);
    }
}


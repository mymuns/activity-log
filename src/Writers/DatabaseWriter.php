<?php

namespace ActivityLog\Writers;

use Illuminate\Support\Facades\DB;

class DatabaseWriter implements LogWriterInterface
{
    public function write(array $data): void
    {
        DB::table('activity_logs')->insert([
            'type'          => $data['type'] ?? 'http',
            'event'         => $data['event'] ?? null,
            'model'         => $data['model'] ?? null,
            'model_id'      => $data['model_id'] ?? null,
            'method'        => $data['method'] ?? null,
            'path'          => $data['path'] ?? null,
            'request_body'  => $data['request_body'] ?? null,
            'response_body' => $data['response_body'] ?? null,
            'status_code'   => $data['status_code'] ?? null,
            'ip_address'    => $data['ip_address'] ?? null,
            'user_agent'    => $data['user_agent'] ?? null,
            'user_id'       => $data['user_id'] ?? null,
            'changes'       => $data['changes'] ?? null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}


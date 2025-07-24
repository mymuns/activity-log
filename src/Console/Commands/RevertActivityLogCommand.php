<?php

namespace ActivityLog\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RevertActivityLogCommand extends Command
{
    protected $signature = 'activitylog:revert {log_id}';
    protected $description = 'Revert model to previous state using activity log';

    public function handle(): void
    {
        $logId = $this->argument('log_id');

        $log = DB::table('activity_logs')->where('id', $logId)->first();

        if (!$log) {
            $this->error("Log ID {$logId} not found.");
            return;
        }

        if (!$log->loggable_type || !$log->loggable_id || !$log->changes) {
            $this->error("Invalid or incomplete log data.");
            return;
        }

        $modelClass = $log->loggable_type;

        if (!class_exists($modelClass)) {
            $this->error("Model class {$modelClass} not found.");
            return;
        }

        $model = $modelClass::find($log->loggable_id);

        if (!$model) {
            $this->error("Model with ID {$log->loggable_id} not found.");
            return;
        }

        $changes = json_decode($log->changes, true);

        foreach ($changes as $field => $values) {
            if (array_key_exists('old', $values)) {
                $model->{$field} = $values['old'];
            }
        }

        $model->save();

        $this->info("Model reverted to previous state using log ID {$logId}.");
    }
}

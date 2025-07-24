<?php

namespace ActivityLog\Traits;

use Illuminate\Support\Facades\Auth;

trait LogsModelActivity
{
    public static function bootLogsModelActivity(): void
    {
        static::created(fn ($model) => $model->logActivity('created'));
        static::updated(fn ($model) => $model->logActivity('updated'));
        static::deleted(fn ($model) => $model->logActivity('deleted'));
    }

    protected function logActivity(string $event): void
    {
        $attributes = static::$logAttributes ?? $this->getFillable();
        $onlyDirty  = static::$logOnlyDirty ?? false;

        $changes = $onlyDirty ? $this->getDirty() : $this->only($attributes);

        if (empty($changes)) return;

        $loggerData = [
            'event'     => $event,
            'model'     => static::class,
            'model_id'  => $this->getKey(),
            'user_id'   => Auth::id(),
            'changes'   => json_encode($changes),
            'old_values'=> json_encode($this->getOriginal($attributes)),
            'new_values'=> json_encode($changes),
        ];

        activity_logger()->logModelEvent($loggerData);
    }
}


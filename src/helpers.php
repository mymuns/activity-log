<?php

if (!function_exists('activity_logger')) {
    function activity_logger(): \ActivityLog\Services\ActivityLoggerService {
        return app('activity_logger');
    }
}


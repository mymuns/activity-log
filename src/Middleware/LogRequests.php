<?php

namespace ActivityLog\Middleware;

use Spatie\HttpLogger\Middlewares\LogRequests as BaseLogRequests;
use ActivityLog\Services\ActivityLoggerService;
use Closure;

class LogRequests extends BaseLogRequests
{
    public function handle($request, Closure $next)
    {
        if (!config('activitylog.enabled')) {
            return $next($request);
        }

        $response = parent::handle($request, $next);

        /** @var ActivityLoggerService $logger */
        $logger = app(ActivityLoggerService::class);

        $logger->logRequestResponse($request, $response);

        return $response;
    }
}


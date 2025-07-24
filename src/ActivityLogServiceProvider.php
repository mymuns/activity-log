<?php

namespace ActivityLog;

use Illuminate\Support\ServiceProvider;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/activitylog.php', 'activitylog');

        $this->app->bind('activity_logger', function () {
            return new Services\ActivityLoggerService();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/activitylog.php' => config_path('activitylog.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if (config('activitylog.enabled', true)) {
            $this->registerRoutes();
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                \ActivityLog\Console\RevertActivityLogCommand::class,
            ]);
        }
    }
    protected function registerRoutes()
    {
        Route::middleware(['api', 'auth:api'])
        ->prefix('api') // Uygulamanın api endpointlerine entegre olur
        ->group(function () {
            Route::post('/activity-log/{id}/revert', \ActivityLog\Http\Controllers\RevertController::class)
                ->name('activity-log.revert');
        });
    }
}


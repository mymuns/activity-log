<?php

namespace ActivityLog;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
class ActivityLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/activitylog.php', 'activitylog');

        $this->app->bind('activitylog', function () {
            return new Services\ActivityLoggerService();
        });
    }

    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        $this->publishes([
            __DIR__.'/../config/activitylog.php' => config_path('activitylog.php'),
        ], 'config');

        $logChannel = config('activitylog.driver', 'database');

        if ($logChannel === 'database') {
            // Migration dosyalarını yayınla
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            // Veya istersen publish da edebilirsin:
            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'activitylog-migrations');
        }

        if (config('activitylog.enabled', true)) {
            $this->registerRoutes();
            $router->pushMiddlewareToGroup('api', \ActivityLog\Middleware\LogRequestResponseMiddleware::class);
        }

        $this->commands([
            \ActivityLog\Console\Commands\RevertActivityLogCommand::class,
        ]);
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


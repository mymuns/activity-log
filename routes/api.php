<?php

use Illuminate\Support\Facades\Route;
use ActivityLog\Http\Controllers\RevertController;

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::post('/activity-log/{id}/revert', RevertController::class)
        ->name('activity-log.revert');
});
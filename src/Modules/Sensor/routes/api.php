<?php

use Illuminate\Support\Facades\Route;
use Modules\Sensor\Http\Controllers\SensorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sensors', SensorController::class)->names('sensor');
});

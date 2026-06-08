<?php

use Illuminate\Support\Facades\Route;
use Modules\Sensor\Http\Controllers\SensorDashboardController;
use Modules\Sensor\Http\Controllers\SensorReadingController;
use Modules\Sensor\Http\Controllers\SensorController;
use Modules\Sensor\Http\Controllers\SensorThresholdProfileController;
use Modules\Sensor\Http\Controllers\SensorTypeController;
use Modules\Sensor\Http\Controllers\UnitController;

Route::middleware(['jwt.auth'])->prefix('v1')->group(function () {
    Route::get('sensors/dashboard/summary', [SensorDashboardController::class, 'summary'])->middleware('group.permission:dashboard.view');

    Route::apiResource('sensor-types', SensorTypeController::class)->middleware('group.permission:sensor-types.manage');
    Route::apiResource('units', UnitController::class)->middleware('group.permission:units.manage');
    Route::apiResource('threshold-profiles', SensorThresholdProfileController::class)->middleware('group.permission:thresholds.manage');
    Route::post('threshold-profiles/{threshold_profile}/apply', [SensorThresholdProfileController::class, 'apply'])->middleware('group.permission:thresholds.manage');

    Route::get('sensors', [SensorController::class, 'index'])->middleware('group.permission:sensors.view');
    Route::post('sensors', [SensorController::class, 'store'])->middleware('group.permission:sensors.manage');
    Route::get('sensors/{sensor}', [SensorController::class, 'show'])->middleware('group.permission:sensors.view');
    Route::put('sensors/{sensor}', [SensorController::class, 'update'])->middleware('group.permission:sensors.manage');
    Route::patch('sensors/{sensor}', [SensorController::class, 'update'])->middleware('group.permission:sensors.manage');
    Route::delete('sensors/{sensor}', [SensorController::class, 'destroy'])->middleware('group.permission:sensors.manage');

    Route::get('sensors/{sensor}/readings', [SensorReadingController::class, 'index'])->middleware('group.permission:sensor-readings.view');
    Route::post('sensors/{sensor}/readings', [SensorReadingController::class, 'store'])->middleware('group.permission:sensor-readings.manage');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Sensor\Http\Controllers\SensorController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('sensors', SensorController::class)->names('sensor');
});

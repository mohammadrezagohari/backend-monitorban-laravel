<?php

use Illuminate\Support\Facades\Route;
use Modules\Room\Http\Controllers\RoomController;

Route::middleware(['jwt.auth'])->prefix('v1')->group(function () {
    Route::apiResource('rooms', RoomController::class)->names('room');
});

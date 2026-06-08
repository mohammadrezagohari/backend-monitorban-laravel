<?php

use Illuminate\Support\Facades\Route;
use Modules\Room\Http\Controllers\RoomController;

Route::middleware(['jwt.auth'])->prefix('v1')->group(function () {
    Route::get('rooms', [RoomController::class, 'index'])->middleware('group.permission:rooms.view')->name('room.index');
    Route::post('rooms', [RoomController::class, 'store'])->middleware('group.permission:rooms.manage')->name('room.store');
    Route::get('rooms/{room}', [RoomController::class, 'show'])->middleware('group.permission:rooms.view')->name('room.show');
    Route::put('rooms/{room}', [RoomController::class, 'update'])->middleware('group.permission:rooms.manage')->name('room.update');
    Route::patch('rooms/{room}', [RoomController::class, 'update'])->middleware('group.permission:rooms.manage');
    Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->middleware('group.permission:rooms.manage')->name('room.destroy');
});

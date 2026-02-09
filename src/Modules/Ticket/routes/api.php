<?php

use Illuminate\Support\Facades\Route;
use Modules\Ticket\Http\Controllers\FaqController;
use Modules\Ticket\Http\Controllers\TicketController;


Route::group(['prefix' => 'faq'], function () {
    Route::get('list', [FaqController::class, 'index']);
    Route::get('{id}', [FaqController::class, 'show']);
});

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class)->names('ticket');

    Route::group(['prefix' => 'faq'], function () {
        Route::get('/', [FaqController::class, 'index']);
        Route::post('store', [FaqController::class, 'store']);
        Route::get('{id}', [FaqController::class, 'show']);
        Route::put('{id}', [FaqController::class, 'update']);
        Route::delete('{id}', [FaqController::class, 'destroy']);
    });
});



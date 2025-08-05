<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\UserController;

Route::middleware(['auth:sanctum' , 'role:admin'])->prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class)->names('user');
});

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/dashboard', fn() => response()->json(['message' => 'خوش آمدید ادمین']));
});

Route::middleware(['auth:api', 'permission:manage users'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

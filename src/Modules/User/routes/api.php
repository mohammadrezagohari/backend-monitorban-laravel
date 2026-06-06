<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\GroupController;
use Modules\User\Http\Controllers\PermissionController;
use Modules\User\Http\Controllers\RoleController;
use Modules\User\Http\Controllers\UserController;

Route::prefix('v1')->group(function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('request-otp', [AuthController::class, 'requestOtp']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    });

    Route::middleware(['jwt.auth'])->group(function () {
        Route::apiResource('users', controller: UserController::class)->names('user');

        Route::middleware(['auth:api', 'role:admin,super-admin'])->group(function () {
            Route::get('/dashboard', fn() => response()->json(['message' => 'خوش آمدید ادمین']));

            Route::prefix('permissions' ,)->group(function () {
                Route::post('/', [PermissionController::class, 'store']);
            });

            Route::prefix('roles')->group(function () {
                Route::post('/', [RoleController::class, 'store']);
                Route::get('/', [RoleController::class, 'index']);
            });

            Route::prefix('groups')->group(function () {
                Route::post('/', [GroupController::class, 'store']);
                Route::get('/{id}', [GroupController::class, 'show']);
                Route::put('/{id}/permissions', [GroupController::class, 'updatePermissions']);
            });
        });


    });


})->middleware(['api']);

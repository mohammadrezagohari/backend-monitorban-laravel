<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\GroupController;
use Modules\User\Http\Controllers\PermissionController;
use Modules\User\Http\Controllers\RoleController;
use Modules\User\Http\Controllers\UserController;

Route::middleware(['auth:sanctum', 'role:admin,super-admin'])->prefix('v1')->group(function () {
    Route::apiResource('users', controller: UserController::class)->names('user');
});

Route::middleware(['auth:api', 'role:admin,super-admin'])->group(function () {
    Route::get('/dashboard', fn() => response()->json(['message' => 'خوش آمدید ادمین']));
});

// Route::middleware(['auth:api', 'permission:manage users'])->group(function () {
Route::middleware(['auth:api', 'role:admin,super-admin'])->group(function () {
    Route::apiResource('/users', UserController::class);
});


Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('request-otp', [AuthController::class, 'requestOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

});

Route::get('email', [UserController::class, 'broadcastNewsletter']);


Route::prefix('permissions' ,)->group(function () {
    Route::post('/', [PermissionController::class, 'store']);
})->middleware(['auth:sanctum', 'role:admin,super-admin']);

Route::prefix('roles')->group(function () {
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/', [RoleController::class, 'index']);
})->middleware(['auth:sanctum', 'role:admin,super-admin']);

Route::prefix('groups')->group(function () {
    Route::post('/', [GroupController::class, 'store']);
    Route::get('/{id}', [GroupController::class, 'show']);
    Route::put('/{id}/permissions', [GroupController::class, 'updatePermissions']);
})->middleware(['auth:sanctum', 'role:admin,super-admin']);
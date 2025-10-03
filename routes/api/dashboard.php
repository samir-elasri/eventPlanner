<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Dashboard\AuthController;
use App\Http\Controllers\Api\Dashboard\EventController;
use App\Http\Controllers\Api\Dashboard\RegistrationController;
use App\Http\Controllers\Api\Dashboard\UserController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/', [AuthController::class, 'loggedInUser']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'events'], function () {
        Route::get('/', [EventController::class, 'index']);
        Route::post('/create', [EventController::class, 'create']);
        Route::patch('/update', [EventController::class, 'update']);
        Route::get('/{event_id}', [EventController::class, 'show']);
        Route::delete('/{event_id}', [EventController::class, 'delete']);
        Route::post('/{event_id}/toggle-status', [EventController::class, 'toggleStatus']);
    });

    Route::group(['prefix' => 'users'], function () {
        Route::post('/', [UserController::class, 'create']);
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{user_id}', [UserController::class, 'show']);
        Route::patch('/{user_id}', [UserController::class, 'update']);
        Route::delete('/{user_id}', [UserController::class, 'delete']);
        Route::post('/{user_id}/toggle-role', [UserController::class, 'toggleRole']);
    });

    Route::group(['prefix' => 'registration'], function () {
        Route::post('/', [RegistrationController::class, 'create']);
        Route::get('/', [RegistrationController::class, 'index']);
        Route::get('/{registration_id}', [RegistrationController::class, 'show']);
        Route::patch('/{registration_id}', [RegistrationController::class, 'update']);
        Route::delete('/{registration_id}', [RegistrationController::class, 'delete']);
    });
});
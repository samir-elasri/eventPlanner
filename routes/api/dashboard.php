<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Dashboard\AuthController;
use App\Http\Controllers\Api\Dashboard\EventController;

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
});
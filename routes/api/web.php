<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Web\AuthController;
use App\Http\Controllers\Api\Web\EventController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/', [AuthController::class, 'loggedInUser']);

    Route::group(['prefix' => 'profile', 'middleware' => 'auth:sanctum'], function () {
        Route::patch('/', [AuthController::class, 'update']);
        Route::delete('/', [AuthController::class, 'delete']);
    });
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'events'], function () {
        Route::post('/{event_id}/join', [EventController::class, 'joinEvent']);
        Route::post('/{event_id}/cancel', [EventController::class, 'cancelEvent']);
        Route::get('/index', [EventController::class, 'index']);
    });
});
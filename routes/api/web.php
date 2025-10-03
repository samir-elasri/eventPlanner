<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Web\AuthController;
use App\Http\Controllers\Api\Web\RegistrationController;
use App\Http\Controllers\Api\Web\EventController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/', [AuthController::class, 'loggedInUser']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'events'], function () {
        Route::get('/index', [EventController::class, 'index']);
    });

    Route::group(['prefix' => 'registrations'], function () {
        Route::get('/index', [RegistrationController::class, 'index']);
        Route::post('/{event_id}/join', [RegistrationController::class, 'joinRegistration']);
        Route::post('/{registration_id}/cancel', [RegistrationController::class, 'cancelRegistration']);
    });
});
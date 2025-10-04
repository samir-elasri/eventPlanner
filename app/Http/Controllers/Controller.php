<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Event Planner API",
 *     description="API documentation for the Event Planner application"
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local development server"
 * )
 */
abstract class Controller
{
    use AuthorizesRequests;
}

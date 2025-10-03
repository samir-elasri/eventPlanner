<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\Dashboard\User\UserResource;
use App\Repositories\Dashboard\AuthRepository;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }
    
    /**
     * @OA\Post(
     *     path="/api/dashboard/auth/login",
     *     summary="Dashboard admin login",
     *     @OA\Response(response=200, description="Admin logged in successfully")
     * )
     */
    public function login(Request $request)
    {
        $params = $request->all();
        return $this->authRepository->login($params);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/auth/user",
     *     summary="Get logged in admin user details",
     *     @OA\Response(response=200, description="Admin user details retrieved successfully")
     * )
     */
    public function loggedInUser()
    {
        return $this->authRepository->loggedInUser();
    }

    /**
     * @OA\Post(
     *     path="/api/dashboard/auth/logout",
     *     summary="Dashboard admin logout",
     *     @OA\Response(response=200, description="Admin logged out successfully")
     * )
     */
    public function logout()
    {
        return $this->authRepository->logout();
    }
}

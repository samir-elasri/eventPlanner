<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\Web\User\UserResource;
use App\Repositories\Web\AuthRepository;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/web/register",
     *     summary="Register a new user",
     *     @OA\Response(response=201, description="User registered successfully")
     * )
     */
    public function register(Request $request)
    {
        $params = $request->all();
        return $this->authRepository->register($params);
    }

    /**
     * @OA\Post(
     *     path="/api/web/login",
     *     summary="Login user",
     *     @OA\Response(response=200, description="User logged in successfully")
     * )
     */
    public function login(Request $request)
    {
        $params = $request->all();
        return $this->authRepository->login($params);
    }

    /**
     * @OA\Get(
     *     path="/api/web/user",
     *     summary="Get logged in user details",
     *     @OA\Response(response=200, description="User details retrieved successfully")
     * )
     */
    public function loggedInUser()
    {
        return $this->authRepository->loggedInUser();
    }

    /**
     * @OA\Post(
     *     path="/api/web/logout",
     *     summary="Logout user",
     *     @OA\Response(response=200, description="User logged out successfully")
     * )
     */
    public function logout()
    {
        return $this->authRepository->logout();
    }
}

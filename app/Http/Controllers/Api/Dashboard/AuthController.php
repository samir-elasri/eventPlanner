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
    
    public function login(Request $request)
    {
        $params = $request->all();
        return $this->authRepository->login($params);
    }

    public function loggedInUser()
    {
        return $this->authRepository->loggedInUser();
    }

    public function logout()
    {
        return $this->authRepository->logout();
    }
}

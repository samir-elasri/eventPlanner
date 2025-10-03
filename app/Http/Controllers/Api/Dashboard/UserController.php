<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Dashboard\User\UsersResource;
use App\Http\Resources\Dashboard\Users\UserResource;
use App\Repositories\Dashboard\UserRepository;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return $this->userRepository->all();
    }

    public function create(Request $request)
    {
        $params = $request->all();
        return $this->userRepository->create($params);
    }

    public function show(Request $request)
    {
        return $this->userRepository->show($request->user_id);
    }

    public function update(Request $request)
    {
        $params = $request->all();
        return $this->userRepository->create($params, $request->user_id);
    }

    public function destroy(Request $request)
    {
        return $this->userRepository->destroy($request->user_id);
    }

    public function toggleRole(Request $request)
    {
        return $this->userRepository->destroy($request->user_id);
    }
}

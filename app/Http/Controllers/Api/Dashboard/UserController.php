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

    /**
     * @OA\Get(
     *     path="/api/dashboard/users",
     *     summary="Get all users",
     *     @OA\Response(response=200, description="Users retrieved successfully")
     * )
     */
    public function index()
    {
        return $this->userRepository->all();
    }

    /**
     * @OA\Post(
     *     path="/api/dashboard/users",
     *     summary="Create a new user",
     *     @OA\Response(response=201, description="User created successfully")
     * )
     */
    public function create(Request $request)
    {
        $params = $request->all();
        return $this->userRepository->create($params);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/users/{user_id}",
     *     summary="Get user by ID",
     *     @OA\Response(response=200, description="User retrieved successfully")
     * )
     */
    public function show(Request $request)
    {
        return $this->userRepository->show($request->user_id);
    }

    /**
     * @OA\Put(
     *     path="/api/dashboard/users/{user_id}",
     *     summary="Update user",
     *     @OA\Response(response=200, description="User updated successfully")
     * )
     */
    public function update(Request $request)
    {
        $params = $request->all();
        return $this->userRepository->create($params, $request->user_id);
    }

    /**
     * @OA\Delete(
     *     path="/api/dashboard/users/{user_id}",
     *     summary="Delete user",
     *     @OA\Response(response=200, description="User deleted successfully")
     * )
     */
    public function destroy(Request $request)
    {
        return $this->userRepository->destroy($request->user_id);
    }

    /**
     * @OA\Put(
     *     path="/api/dashboard/users/{user_id}/toggle-role",
     *     summary="Toggle user role",
     *     @OA\Response(response=200, description="User role toggled successfully")
     * )
     */
    public function toggleRole(Request $request)
    {
        return $this->userRepository->destroy($request->user_id);
    }
}

<?php

namespace App\Repositories\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Dashboard\User\UsersResource;
use App\Http\Resources\Dashboard\Users\UserResource;

class UserRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $users =  User::paginate(15);
            $data = [
                'users' => UsersResource::collection($users),
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'total_pages' => $users->lastPage(),
                    'next_page_url' => $users->nextPageUrl(),
                    'prev_page_url' => $users->previousPageUrl()
                ]
            ];
            return $data;
        } catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create a newly created resource in storage.
     */
    public function create(Request $request)
    {
        try {
            $validatedFields = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validatedFields['name'],
                'email' => $validatedFields['email'],
                'password' => bcrypt($validatedFields['password']),
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'status' => 201,
                'message' => 'User created successfully',
                'data' => new UserResource($user)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            return [
                'data' => new UserResource($user)
            ];
        } catch (ModelNotFoundException $e) {
            $errorMessage = "User Not Found!";
            return [
                'error' => $errorMessage,
                'status' => 404
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            $validatedFields = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|nullable|string|min:8',
            ]);

            if (isset($validatedFields['password'])) {
                $validatedFields['password'] = bcrypt($validatedFields['password']);
            } else {
                unset($validatedFields['password']);
            }

            $user->update($validatedFields);

            return response()->json([
                'status' => 200,
                'message' => 'User updated successfully',
                'data' => new UserResource($user)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'error' => 'User not found!'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $user->delete();
            return response()->json([
                'status' => 200,
                'message' => "User deleted successfully",
            ], 200);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "User not found!";
            return response()->json([
                'status' => 404,
                'message' => $errorMessage,
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Toggle role between 'user' and 'admin'
    public function toggleRole($user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            if ($user->hasRole('admin')) {
                $user->removeRole('admin');
                $user->assignRole('user');
            } else {
                $user->removeRole('user');
                $user->assignRole('admin');
            }

            return response()->json([
                'status' => 200,
                'message' => "User role toggled to '$newRole' successfully",
            ], 200);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "User not found!";
            return response()->json([
                'status' => 404,
                'message' => $errorMessage,
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

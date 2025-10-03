<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Dashboard\User\UsersResource;
use App\Http\Resources\Dashboard\Users\UserResource;

class UserController extends Controller
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
            $user = User::create([
                "event_id" => $request->input('event_id'),
                "user_id" => $request->input('user_id'),
                "status" => $request->input('status', 'pending'),
                "joined_at" => now()
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
    public function show(User $user)
    {
        try {
            $user = Booking::findOrFail($user_id);
            return [
                'data' => new UserResource($booking)
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
    public function update(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);

            $user->update($request->only([
                'event_id',
                'user_id',
                'status',
                'joined_at'
            ]));

            return response()->json([
                'status' => 200,
                'message' => 'User updated successfully',
                'data' => new UserResource($user)
            ], 200);
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
    public function destroy(User $user)
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

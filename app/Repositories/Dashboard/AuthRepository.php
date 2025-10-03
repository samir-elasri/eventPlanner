<?php

namespace App\Repositories\Dashboard;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\Dashboard\User\UserResource;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function login($params)
    {
        try {
            $user = User::where([['email', $params['email']]])->first();

            if (
                !$user ||
                !Hash::check($params['password'], $user->password) ||
                !$user->hasRole('admin')
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have entered an invalid email, password, or do not have admin access.',
                    'data' => null
                ], 401);
            }

            $token = $user->createToken('Personal Access Token')?->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Admin signed in successfully.',
                'data' => ["token" => $token]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function loggedInUser()
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Logged In User',
                'data' => new UserResource(Auth::user())
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function logout()
    {
        try {
            Auth::user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Log out successfully.',
                'data' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}

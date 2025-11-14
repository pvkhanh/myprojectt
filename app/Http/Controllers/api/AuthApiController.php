<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Api\AuthApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApiController extends Controller
{
    protected AuthApiService $authService;

    public function __construct(AuthApiService $authService)
    {
        $this->authService = $authService;
    }

    // ------------------- REGISTER -------------------
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Register successfully!',
            'data'    => $user
        ], 201);
    }

    // ------------------- LOGIN -------------------
    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login or password.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'data'    => [
                'user'  => $result['user'],
                'token' => $result['token']
            ]
        ]);
    }

    // ------------------- LOGOUT -------------------
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully!'
        ]);
    }

    // ------------------- CURRENT USER -------------------
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user()
        ]);
    }
}

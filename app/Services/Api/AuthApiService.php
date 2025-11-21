<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthApiService
{
    // Xử lý đăng ký
    public function register($data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'buyer',
            'is_active' => true,
        ]);
    }

    // Xử lý đăng nhập
    public function login($data)
    {
        $login = $data['login'];
        $password = $data['password'];

        $user = User::where('email', $login)
            ->orWhere('username', $login)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        // Tạo token cho API (Sanctum)
        $token = $user->createToken('api_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function register(Request $requeset)
    {
        $requeset->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,driver',
        ]);

        $user = User::create([
            'name' => $requeset->name,
            'email' => $requeset->email,
            'password' => $requeset->password,
        ]);

        $user->assignRole($requeset->role);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'role' => $user->getRoleNames(),
            'token' => $token,
        ]);
    }


    public function login(Request $requeset)
    {
        if (!Auth::attempt($requeset->only('email', 'password'))) {
            return response()->json([
                'message' => "Invalid credentials"
            ], 401);
        }

        $user = User::where('email', $requeset->email)->first();

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $requeset)
    {
        $requeset->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => "Logout successfully!"
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        $user = User::create($request->all());

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            "user" => new UserResource($user),
            "token" => $token
        ]);
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                "message" => "Wrong email or password"
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            "user" => new UserResource($user),
            "token" => $token
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }
}

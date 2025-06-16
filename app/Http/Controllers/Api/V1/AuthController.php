<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 *
 * Контроллер, отвечающий за регистрацию, авторизацию и выход пользователя.
 */
class AuthController extends Controller
{
    /**
     * Зарегистрировать нового пользователя.
     *
     * @param  StoreUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return $this->respondWithToken($user);
    }

    /**
     * Войти в систему и получить токен.
     *
     * @param  LoginUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginUserRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                "message" => "Wrong email or password"
            ], 401);
        }

        $user = Auth::user();

        return $this->respondWithToken($user);
    }

    /**
     * Выйти из системы (удалить текущий токен).
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }

    /**
     * Сформировать ответ с токеном доступа.
     *
     * @param  User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithToken(User $user)
    {
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            "user" => new UserResource($user),
            "token" => $token
        ]);
    }
}

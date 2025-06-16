<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на регистрацию нового пользователя.
 *
 * Валидирует входящие данные:
 * - `name` — обязательное поле, строка, максимум 255 символов;
 * - `email` — обязательное поле, валидный email, уникален в таблице `users`, максимум 255 символов;
 * - `password` — обязательное поле, строка.
 *
 * Авторизация:
 * Доступ открыт всем (в том числе неавторизованным), чтобы зарегистрироваться.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Определяет, имеет ли пользователь право отправить этот запрос.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для регистрации пользователя.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string'],
        ];
    }
}

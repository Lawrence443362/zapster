<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на авторизацию пользователя.
 *
 * Валидирует входящие данные:
 * - `email` — обязательный, должен быть валидным email-адресом;
 * - `password` — обязательный, строка.
 *
 * Авторизация:
 * Доступ открыт всем (в том числе неавторизованным), чтобы можно было войти в систему.
 */
class LoginUserRequest extends FormRequest
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
     * Правила валидации для входа пользователя.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}

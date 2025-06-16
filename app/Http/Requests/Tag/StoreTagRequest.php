<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на создание нового тега.
 *
 * Валидирует входящие данные:
 * - `name` — обязательное поле, строка, не длиннее 255 символов.
 *
 * Авторизация:
 * Запрос разрешён всем авторизованным пользователям (по умолчанию — всем).
 */
class StoreTagRequest extends FormRequest
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
     * Правила валидации для запроса на создание тега.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}

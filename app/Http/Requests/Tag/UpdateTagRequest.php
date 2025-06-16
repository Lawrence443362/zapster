<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на обновление существующего тега.
 *
 * Валидирует входящие данные:
 * - `name` — обязательное поле, строка, максимум 255 символов.
 *
 * Авторизация:
 * Разрешён всем пользователям (можно дополнительно ограничить при необходимости).
 */
class UpdateTagRequest extends FormRequest
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
     * Правила валидации для обновления тега.
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

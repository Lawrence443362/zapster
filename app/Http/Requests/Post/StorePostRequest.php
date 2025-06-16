<?php

namespace App\Http\Requests\Post;

use App\Models\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * Запрос на создание нового поста.
 *
 * Валидирует входящие данные при создании поста:
 * - `title` — обязательный, строка до 255 символов;
 * - `description` — обязательный, строка до 255 символов;
 * - `status` — обязательный, одно из значений перечисления PostStatus (enum);
 * - `tags` — обязательный массив тегов, минимум 1, без дубликатов;
 * - `tags.*` — каждый тег в массиве должен быть строкой.
 */
class StorePostRequest extends FormRequest
{
    /**
     * Определяет, авторизован ли пользователь на выполнение запроса.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для запроса.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'status' => ['required', new Enum(PostStatus::class)],
            'tags' => ['required', 'array', 'min:1', 'distinct'],
            'tags.*' => ['string'],
        ];
    }
}

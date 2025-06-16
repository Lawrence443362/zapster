<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

/**
 * Запрос на удаление поста.
 *
 * Проверяет, что текущий пользователь является автором поста.
 * Если не авторизован — выбрасывает исключение с ответом 403.
 *
 * Валидационные правила отсутствуют, так как сам пост передаётся через route-model binding,
 * и дополнительных данных в теле запроса не требуется.
 */
class DeletePostRequest extends FormRequest
{
    /**
     * Проверяет, имеет ли пользователь право удалить пост.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $post = $this->route("post");

        return Auth::user()->id === $post->user_id;
    }

    /**
     * Обработка случая, когда авторизация не удалась.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Access denied',
        ], 403));
    }

    /**
     * Правила валидации для запроса.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Нет входных данных для валидации
        ];
    }
}

<?php

namespace App\Http\Requests\Post;

use App\Models\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

/**
 * Запрос на обновление существующего поста.
 *
 * Валидирует данные, переданные при обновлении поста:
 * - `title` — необязательный, строка до 255 символов;
 * - `description` — необязательный, строка;
 * - `status` — необязательный, одно из значений перечисления PostStatus (enum);
 * - `tags` — обязательный массив тегов (минимум 1, уникальные);
 * - `tags.*` — каждый тег должен быть строкой.
 *
 * Авторизация:
 * Только владелец поста может обновить его.
 * В случае неавторизованного доступа возвращается 403 с сообщением `Access denied`.
 */
class UpdatePostRequest extends FormRequest
{
    /**
     * Определяет, имеет ли текущий пользователь право обновить пост.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $post = $this->route("post");
        return Auth::user()->id === $post->user_id;
    }

    /**
     * Обработка неудачной авторизации.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Access denied',
        ], 403));
    }

    /**
     * Правила валидации данных для обновления поста.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['string', 'max:255'],
            'description' => ['string'],
            'status' => ['string', new Enum(PostStatus::class)],
            'tags' => ['required', 'array', 'min:1', 'distinct'],
            'tags.*' => ['string'],
        ];
    }
}

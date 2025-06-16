<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на прикрепление аудиофайла к посту.
 *
 * Выполняет валидацию входящего файла:
 * - файл обязателен (`required`);
 * - должен быть типом audio/mpeg (MP3);
 * - максимальный размер — 20 МБ.
 *
 * Пример поля:
 * - audio: загружаемый MP3-файл.
 */
class AttachAudioToPostRequest extends FormRequest
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
            'audio' => ['required', 'file', 'mimetypes:audio/mpeg', 'max:20480'],
        ];
    }
}

<?php

return [
    // Диск (storage disk) для хранения оригинальных аудиофайлов
    'disk' => env('AUDIO_DISK', 'public'),

    // Папка на диске для хранения оригинальных аудиофайлов
    'folder' => env('AUDIO_FOLDER', '/posts/audio'),

    // Диск для хранения сжатых аудиофайлов
    'compressed_disk' => env('AUDIO_COMPRESSED_DISK', 'public'),

    // Папка на диске для хранения сжатых аудиофайлов
    'compressed_folder' => env('AUDIO_COMPRESSED_FOLDER', '/posts/audio_compressed'),
];

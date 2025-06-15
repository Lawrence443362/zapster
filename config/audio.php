<?php

return [
    'disk' => env('AUDIO_DISK', 'public'),
    'folder' => env('AUDIO_FOLDER', '/posts/audio'),
    'compressed_disk' => env('AUDIO_COMPRESSED_DISK', 'public'),
    'compressed_folder' => env('AUDIO_COMPRESSED_FOLDER', '/posts/audio_compressed'),
];


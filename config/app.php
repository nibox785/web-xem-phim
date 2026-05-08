<?php

return [
    'app_name' => 'WEB Xem Phim NiBoX',
    'app_url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'app_debug' => getenv('APP_DEBUG') ?: false,
    'timezone' => 'UTC+07',
    'app_path' => dirname(__DIR__) . '/app',
    'public_path' => dirname(__DIR__) . '/public',
    'views_path' => dirname(__DIR__) . '/app/Views',
];

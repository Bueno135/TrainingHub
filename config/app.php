<?php
// ============================================
// config/app.php
// ============================================
require_once __DIR__ . '/env.php';

return [
    'app_name' => env('APP_NAME', 'TrainingHub'),
    'app_url' => env('APP_URL', 'http://localhost'),
    'app_env' => env('APP_ENV', 'development'),
    'timezone' => env('TIMEZONE', 'America/Sao_Paulo'),
    'locale' => env('LOCALE', 'pt_BR'),
    'debug' => env('APP_DEBUG', true),
    'session_lifetime' => env('SESSION_LIFETIME', 7200)
];


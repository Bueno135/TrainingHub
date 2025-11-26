<?php
// ============================================
// config/email.php
// ============================================
require_once __DIR__ . '/env.php';

return [
    'from_email' => env('MAIL_FROM_EMAIL', 'noreply@traininghub.com'),
    'from_name' => env('MAIL_FROM_NAME', 'TrainingHub'),
    'smtp_enabled' => env('MAIL_SMTP_ENABLED', false),
    'smtp_host' => env('MAIL_SMTP_HOST', 'smtp.gmail.com'),
    'smtp_port' => env('MAIL_SMTP_PORT', 587),
    'smtp_username' => env('MAIL_SMTP_USERNAME', ''),
    'smtp_password' => env('MAIL_SMTP_PASSWORD', ''),
    'smtp_encryption' => env('MAIL_SMTP_ENCRYPTION', 'tls')
];


<?php
// ============================================
// config/email.php
// ============================================
return [
    'from_email' => 'noreply@traininghub.com',
    'from_name' => 'TrainingHub',
    'smtp_enabled' => false, // Para usar SMTP, configure abaixo
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => '',
    'smtp_password' => '',
    'smtp_encryption' => 'tls'
];


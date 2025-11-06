<?php

// ============================================
// public/index.php (FRONT CONTROLLER)
// ============================================
session_start();

// Autoload simples
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../src/Services/' . $class . '.php',
        __DIR__ . '/../src/Controllers/' . $class . '.php',
        __DIR__ . '/../config/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Inicializar database
$database = new Database();
$db = $database->connect();
$authService = new AuthService($db);

// Roteamento simples
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'login':
        include __DIR__ . '/../src/Views/auth/login.php';
        break;
    
    case 'register':
        include __DIR__ . '/../src/Views/auth/register.php';
        break;
    
    case 'logout':
        $authService->logout();
        header('Location: index.php?page=login');
        exit;
    
    case 'dashboard':
        if (!$authService->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }
        $user = $authService->getCurrentUser();
        include __DIR__ . '/../src/Views/dashboard/index.php';
        break;
    
    default:
        include __DIR__ . '/../src/Views/home.php';
        break;
}
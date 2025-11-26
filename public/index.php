<?php

// ============================================
// public/index.php (FRONT CONTROLLER)
// ============================================

// Carregar variáveis de ambiente
require_once __DIR__ . '/../config/env.php';

// Configurar timezone
$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);

session_start();

// Autoload simples
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../src/Models/' . $class . '.php',
        __DIR__ . '/../src/Controller/' . $class . '.php',
        __DIR__ . '/../src/Controllers/' . $class . '.php',
        __DIR__ . '/../src/Services/' . $class . '.php',
        __DIR__ . '/../src/Repositories/' . $class . '.php',
        __DIR__ . '/../src/Validators/' . $class . '.php',
        __DIR__ . '/../src/Database/' . $class . '.php',
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
require_once __DIR__ . '/../config/database.php';
$database = new Database();
$db = $database->connect();

// Inicializar serviços
require_once __DIR__ . '/../src/Services/AuthService.php';
$authService = new AuthService($db);

// Roteamento simples
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? null;

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
    
    case 'perfil':
        if (!$authService->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }
        $user = $authService->getCurrentUser();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($user['tipo'] === 'professor') {
                require_once __DIR__ . '/../src/Controller/ProfessorController.php';
                $controller = new ProfessorController($db);
                $result = $controller->updateProfile($user['id'], $_POST);
                if ($result['success']) {
                    header('Location: index.php?page=perfil&success=1');
                    exit;
                }
                $error = $result['message'] ?? 'Erro ao atualizar perfil';
            } else {
                require_once __DIR__ . '/../src/Controller/AcademiaController.php';
                $controller = new AcademiaController($db);
                $result = $controller->updateProfile($user['id'], $_POST);
                if ($result['success']) {
                    header('Location: index.php?page=perfil&success=1');
                    exit;
                }
                $error = $result['message'] ?? 'Erro ao atualizar perfil';
            }
        }
        
        include __DIR__ . '/../src/Views/perfil/index.php';
        break;
    
    case 'freelances':
        if (!$authService->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }
        $user = $authService->getCurrentUser();
        
        require_once __DIR__ . '/../src/Controller/FreelanceController.php';
        $freelanceController = new FreelanceController($db);
        
        if ($action === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($user['tipo'] !== 'academia') {
                header('Location: index.php?page=dashboard');
                exit;
            }
            
            $stmt = $db->prepare("SELECT id FROM academias WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $academia = $stmt->fetch();
            
            if ($academia) {
                $result = $freelanceController->create($academia['id'], $_POST);
                if ($result['success']) {
                    header('Location: index.php?page=freelances&success=1');
                    exit;
                }
                $error = $result['message'] ?? 'Erro ao criar freelance';
            }
        }
        
        if ($user['tipo'] === 'academia') {
            $stmt = $db->prepare("SELECT id FROM academias WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $academia = $stmt->fetch();
            $freelances = $academia ? $freelanceController->getByAcademia($academia['id']) : [];
        } else {
            $freelances = $freelanceController->search($_GET);
        }
        
        include __DIR__ . '/../src/Views/freelances/index.php';
        break;
    
    case 'propostas':
        if (!$authService->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }
        $user = $authService->getCurrentUser();
        
        if ($action === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($user['tipo'] !== 'professor') {
                header('Location: index.php?page=dashboard');
                exit;
            }
            
            require_once __DIR__ . '/../src/Controller/ProfessorController.php';
            $controller = new ProfessorController($db);
            
            $stmt = $db->prepare("SELECT id FROM professores WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $professor = $stmt->fetch();
            
            if ($professor) {
                $result = $controller->createProposta(
                    $professor['id'],
                    $_POST['freelance_id'],
                    $_POST['mensagem'] ?? '',
                    $_POST['valor_proposto'] ?? null
                );
                if ($result['success']) {
                    header('Location: index.php?page=propostas&success=1');
                    exit;
                }
                $error = $result['message'] ?? 'Erro ao enviar proposta';
            }
        }
        
        if ($action === 'responder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($user['tipo'] !== 'academia') {
                header('Location: index.php?page=dashboard');
                exit;
            }
            
            $stmt = $db->prepare("SELECT id FROM academias WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $academia = $stmt->fetch();
            
            if ($academia) {
                $result = $freelanceController->responderProposta(
                    $_POST['proposta_id'],
                    $academia['id'],
                    $_POST['acao']
                );
                if ($result['success']) {
                    header('Location: index.php?page=propostas&success=1');
                    exit;
                }
                $error = $result['message'] ?? 'Erro ao responder proposta';
            }
        }
        
        include __DIR__ . '/../src/Views/propostas/index.php';
        break;
    
    default:
        include __DIR__ . '/../src/Views/home.php';
        break;
}
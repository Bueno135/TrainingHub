<?php

// ============================================
// src/Controllers/AuthController.php
// ============================================
class AuthController {
    private $authService;

    public function __construct($authService) {
        $this->authService = $authService;
    }

    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $tipo = $_POST['tipo'] ?? '';

        // Validar confirmação de senha
        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'As senhas não coincidem'];
        }

        return $this->authService->register($email, $password, $tipo);
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = $this->authService->login($email, $password);
        
        if ($result['success']) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        return $result;
    }
}
<?php
// ============================================
// src/Services/AuthService.php
// ============================================
class AuthService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($email, $password, $tipo) {
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }

        // Validar tipo
        if (!in_array($tipo, ['professor', 'academia'])) {
            return ['success' => false, 'message' => 'Tipo de usuário inválido'];
        }

        // Validar senha
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Senha deve ter no mínimo 6 caracteres'];
        }

        // Verificar se email já existe
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email já cadastrado'];
        }

        // Criar usuário
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (email, password_hash, tipo) VALUES (?, ?, ?)");
        
        try {
            $stmt->execute([$email, $passwordHash, $tipo]);
            $userId = $this->db->lastInsertId();

            // Criar registro na tabela específica (professor ou academia)
            if ($tipo === 'professor') {
                $stmt = $this->db->prepare("INSERT INTO professores (user_id) VALUES (?)");
                $stmt->execute([$userId]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO academias (user_id) VALUES (?)");
                $stmt->execute([$userId]);
            }

            return ['success' => true, 'message' => 'Cadastro realizado com sucesso!', 'user_id' => $userId];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao cadastrar: ' . $e->getMessage()];
        }
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT id, email, password_hash, tipo, status FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Email ou senha incorretos'];
        }

        if ($user['status'] !== 'ativo') {
            return ['success' => false, 'message' => 'Usuário inativo ou suspenso'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Email ou senha incorretos'];
        }

        // Iniciar sessão
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_tipo'] = $user['tipo'];
        $_SESSION['logged_in'] = true;

        return ['success' => true, 'message' => 'Login realizado com sucesso!', 'tipo' => $user['tipo']];
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Logout realizado'];
    }

    public function isAuthenticated() {
        session_start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'tipo' => $_SESSION['user_tipo']
        ];
    }
}

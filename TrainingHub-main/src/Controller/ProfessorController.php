<?php
// ============================================
// src/Controller/ProfessorController.php
// ============================================
class ProfessorController {
    private $db;
    private $professorRepository;
    private $userRepository;

    public function __construct($db) {
        $this->db = $db;
        require_once __DIR__ . '/../Repositories/ProfessorRepository.php';
        require_once __DIR__ . '/../Repositories/UserRepository.php';
        $this->professorRepository = new ProfessorRepository($db);
        $this->userRepository = new UserRepository($db);
    }

    public function updateProfile($userId, $data) {
        // Validar dados
        $errors = [];

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }

        if (!empty($data['cpf']) && !$this->validateCPF($data['cpf'])) {
            $errors[] = 'CPF inválido';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Atualizar perfil do professor
        $professorData = [
            'nome' => $data['nome'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'cpf' => $data['cpf'] ?? null,
            'cref' => $data['cref'] ?? null,
            'especialidades' => $data['especialidades'] ?? null,
            'experiencia' => $data['experiencia'] ?? null,
            'formacao' => $data['formacao'] ?? null,
            'disponibilidade' => $data['disponibilidade'] ?? null,
            'valor_hora' => !empty($data['valor_hora']) ? floatval($data['valor_hora']) : null,
            'cidade' => $data['cidade'] ?? null,
            'estado' => $data['estado'] ?? null,
            'endereco' => $data['endereco'] ?? null
        ];

        $result = $this->professorRepository->updateByUserId($userId, $professorData);

        if ($result) {
            return ['success' => true, 'message' => 'Perfil atualizado com sucesso!'];
        }

        return ['success' => false, 'message' => 'Erro ao atualizar perfil'];
    }

    public function getProfile($userId) {
        return $this->professorRepository->findByUserId($userId);
    }

    public function search($filters) {
        return $this->professorRepository->search($filters);
    }

    public function createProposta($professorId, $freelanceId, $mensagem, $valorProposto) {
        // Verificar se já existe proposta
        $stmt = $this->db->prepare("SELECT id FROM propostas WHERE freelance_id = ? AND professor_id = ?");
        $stmt->execute([$freelanceId, $professorId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Você já enviou uma proposta para este freelance'];
        }

        // Criar proposta
        $stmt = $this->db->prepare("
            INSERT INTO propostas (freelance_id, professor_id, mensagem, valor_proposto) 
            VALUES (?, ?, ?, ?)
        ");
        
        try {
            $stmt->execute([$freelanceId, $professorId, $mensagem, $valorProposto]);
            $propostaId = $this->db->lastInsertId();

            // Criar notificação para a academia
            require_once __DIR__ . '/../Services/NotificationService.php';
            $notificationService = new NotificationService($this->db);
            
            // Buscar dados do freelance e academia
            $freelance = $this->db->prepare("
                SELECT f.titulo, a.user_id, a.nome as academia_nome 
                FROM freelances f 
                INNER JOIN academias a ON f.academia_id = a.id 
                WHERE f.id = ?
            ");
            $freelance->execute([$freelanceId]);
            $freelanceData = $freelance->fetch();

            $professor = $this->professorRepository->findById($professorId);
            $professorNome = $professor['nome'] ?? 'Professor';

            $notificationService->notifyPropostaRecebida(
                $freelanceData['user_id'],
                $professorNome,
                $freelanceData['titulo'],
                $propostaId
            );

            return ['success' => true, 'message' => 'Proposta enviada com sucesso!', 'proposta_id' => $propostaId];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao enviar proposta: ' . $e->getMessage()];
        }
    }

    public function getPropostas($professorId) {
        $stmt = $this->db->prepare("
            SELECT p.*, f.titulo as freelance_titulo, f.descricao as freelance_descricao,
                   a.nome as academia_nome, a.cidade as academia_cidade, a.estado as academia_estado
            FROM propostas p
            INNER JOIN freelances f ON p.freelance_id = f.id
            INNER JOIN academias a ON f.academia_id = a.id
            WHERE p.professor_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$professorId]);
        return $stmt->fetchAll();
    }

    private function validateCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verificar se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Validar dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}


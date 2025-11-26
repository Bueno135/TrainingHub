<?php
// ============================================
// src/Controller/AcademiaController.php
// ============================================
class AcademiaController {
    private $db;
    private $userRepository;

    public function __construct($db) {
        $this->db = $db;
        require_once __DIR__ . '/../Repositories/UserRepository.php';
        $this->userRepository = new UserRepository($db);
    }

    public function updateProfile($userId, $data) {
        // Validar dados
        $errors = [];

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }

        if (!empty($data['cnpj']) && !$this->validateCNPJ($data['cnpj'])) {
            $errors[] = 'CNPJ inválido';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Atualizar email do usuário se fornecido
        if (!empty($data['email'])) {
            $userData = ['email' => $data['email']];
            $this->userRepository->update($userId, $userData);
        }

        // Atualizar perfil da academia
        $stmt = $this->db->prepare("SELECT id FROM academias WHERE user_id = ?");
        $stmt->execute([$userId]);
        $academia = $stmt->fetch();

        if (!$academia) {
            return ['success' => false, 'message' => 'Perfil de academia não encontrado'];
        }

        $academiaData = [
            'nome' => $data['nome'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'cnpj' => $data['cnpj'] ?? null,
            'descricao' => $data['descricao'] ?? null,
            'cidade' => $data['cidade'] ?? null,
            'estado' => $data['estado'] ?? null,
            'endereco' => $data['endereco'] ?? null,
            'website' => $data['website'] ?? null
        ];

        $fields = [];
        $values = [];
        
        foreach ($academiaData as $key => $value) {
            if ($value !== null) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return ['success' => false, 'message' => 'Nenhum dado para atualizar'];
        }

        $values[] = $academia['id'];
        $sql = "UPDATE academias SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute($values);
            return ['success' => true, 'message' => 'Perfil atualizado com sucesso!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()];
        }
    }

    public function getProfile($userId) {
        $stmt = $this->db->prepare("SELECT * FROM academias WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getStats($userId) {
        $stmt = $this->db->prepare("SELECT id FROM academias WHERE user_id = ?");
        $stmt->execute([$userId]);
        $academia = $stmt->fetch();

        if (!$academia) {
            return null;
        }

        $academiaId = $academia['id'];

        // Contar freelances
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM freelances WHERE academia_id = ?");
        $stmt->execute([$academiaId]);
        $totalFreelances = $stmt->fetchColumn();

        // Contar freelances abertos
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM freelances WHERE academia_id = ? AND status = 'aberto'");
        $stmt->execute([$academiaId]);
        $freelancesAbertos = $stmt->fetchColumn();

        // Contar propostas recebidas
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM propostas p
            INNER JOIN freelances f ON p.freelance_id = f.id
            WHERE f.academia_id = ?
        ");
        $stmt->execute([$academiaId]);
        $totalPropostas = $stmt->fetchColumn();

        // Contar professores contratados (propostas aceitas)
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT p.professor_id) FROM propostas p
            INNER JOIN freelances f ON p.freelance_id = f.id
            WHERE f.academia_id = ? AND p.status = 'aceita'
        ");
        $stmt->execute([$academiaId]);
        $professoresContratados = $stmt->fetchColumn();

        // Calcular total investido
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(p.valor_proposto), 0) FROM propostas p
            INNER JOIN freelances f ON p.freelance_id = f.id
            WHERE f.academia_id = ? AND p.status = 'aceita'
        ");
        $stmt->execute([$academiaId]);
        $totalInvestido = $stmt->fetchColumn();

        return [
            'total_freelances' => $totalFreelances,
            'freelances_abertos' => $freelancesAbertos,
            'total_propostas' => $totalPropostas,
            'professores_contratados' => $professoresContratados,
            'total_investido' => $totalInvestido
        ];
    }

    private function validateCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verificar se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Validar primeiro dígito verificador
        $length = 12;
        $digits = substr($cnpj, 0, $length);
        $sum = 0;
        $pos = $length - 7;

        for ($i = 0; $i < $length; $i++) {
            $sum += $digits[$i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        if ($result != $cnpj[12]) {
            return false;
        }

        // Validar segundo dígito verificador
        $length = 13;
        $digits = substr($cnpj, 0, $length);
        $sum = 0;
        $pos = $length - 7;

        for ($i = 0; $i < $length; $i++) {
            $sum += $digits[$i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        return $result == $cnpj[13];
    }
}


<?php
// ============================================
// src/Controller/FreelanceController.php
// ============================================
class FreelanceController {
    private $db;
    private $freelanceRepository;

    public function __construct($db) {
        $this->db = $db;
        require_once __DIR__ . '/../Repositories/FreelanceRepository.php';
        $this->freelanceRepository = new FreelanceRepository($db);
    }

    public function create($academiaId, $data) {
        // Validar dados
        $errors = [];

        if (empty($data['titulo'])) {
            $errors[] = 'Título é obrigatório';
        }

        if (empty($data['descricao'])) {
            $errors[] = 'Descrição é obrigatória';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $freelanceData = [
            'academia_id' => $academiaId,
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'],
            'tipo_trabalho' => $data['tipo_trabalho'] ?? 'presencial',
            'carga_horaria_semanal' => !empty($data['carga_horaria_semanal']) ? intval($data['carga_horaria_semanal']) : null,
            'valor_hora' => !empty($data['valor_hora']) ? floatval($data['valor_hora']) : null,
            'valor_total' => !empty($data['valor_total']) ? floatval($data['valor_total']) : null,
            'requisitos' => $data['requisitos'] ?? null,
            'beneficios' => $data['beneficios'] ?? null,
            'data_inicio' => !empty($data['data_inicio']) ? $data['data_inicio'] : null,
            'data_fim' => !empty($data['data_fim']) ? $data['data_fim'] : null,
            'status' => 'aberto'
        ];

        try {
            $id = $this->freelanceRepository->create($freelanceData);
            return ['success' => true, 'message' => 'Freelance publicado com sucesso!', 'id' => $id];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao publicar freelance: ' . $e->getMessage()];
        }
    }

    public function update($id, $academiaId, $data) {
        // Verificar se o freelance pertence à academia
        $freelance = $this->freelanceRepository->findById($id);
        if (!$freelance || $freelance['academia_id'] != $academiaId) {
            return ['success' => false, 'message' => 'Freelance não encontrado ou sem permissão'];
        }

        $updateData = [];
        $allowedFields = ['titulo', 'descricao', 'tipo_trabalho', 'carga_horaria_semanal', 
                          'valor_hora', 'valor_total', 'requisitos', 'beneficios', 
                          'data_inicio', 'data_fim', 'status'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        try {
            $this->freelanceRepository->update($id, $updateData);
            return ['success' => true, 'message' => 'Freelance atualizado com sucesso!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar freelance: ' . $e->getMessage()];
        }
    }

    public function delete($id, $academiaId) {
        $freelance = $this->freelanceRepository->findById($id);
        if (!$freelance || $freelance['academia_id'] != $academiaId) {
            return ['success' => false, 'message' => 'Freelance não encontrado ou sem permissão'];
        }

        try {
            $this->freelanceRepository->delete($id);
            return ['success' => true, 'message' => 'Freelance removido com sucesso!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao remover freelance: ' . $e->getMessage()];
        }
    }

    public function getById($id) {
        return $this->freelanceRepository->findById($id);
    }

    public function getByAcademia($academiaId) {
        return $this->freelanceRepository->findByAcademiaId($academiaId);
    }

    public function search($filters) {
        return $this->freelanceRepository->findAll($filters);
    }

    public function getPropostas($freelanceId, $academiaId) {
        // Verificar se o freelance pertence à academia
        $freelance = $this->freelanceRepository->findById($freelanceId);
        if (!$freelance || $freelance['academia_id'] != $academiaId) {
            return [];
        }

        $stmt = $this->db->prepare("
            SELECT p.*, pr.nome as professor_nome, pr.cref, pr.nota_media, pr.total_avaliacoes
            FROM propostas p
            INNER JOIN professores pr ON p.professor_id = pr.id
            WHERE p.freelance_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$freelanceId]);
        return $stmt->fetchAll();
    }

    public function responderProposta($propostaId, $academiaId, $acao) {
        // Verificar se a proposta pertence a um freelance da academia
        $stmt = $this->db->prepare("
            SELECT p.*, f.academia_id, f.titulo as freelance_titulo, pr.user_id as professor_user_id, pr.nome as professor_nome
            FROM propostas p
            INNER JOIN freelances f ON p.freelance_id = f.id
            INNER JOIN professores pr ON p.professor_id = pr.id
            WHERE p.id = ?
        ");
        $stmt->execute([$propostaId]);
        $proposta = $stmt->fetch();

        if (!$proposta || $proposta['academia_id'] != $academiaId) {
            return ['success' => false, 'message' => 'Proposta não encontrada ou sem permissão'];
        }

        if (!in_array($acao, ['aceita', 'rejeitada'])) {
            return ['success' => false, 'message' => 'Ação inválida'];
        }

        // Atualizar status da proposta
        $stmt = $this->db->prepare("UPDATE propostas SET status = ? WHERE id = ?");
        $stmt->execute([$acao, $propostaId]);

        // Se aceita, fechar o freelance
        if ($acao === 'aceita') {
            $this->freelanceRepository->updateStatus($proposta['freelance_id'], 'fechado');
            
            // Rejeitar outras propostas do mesmo freelance
            $stmt = $this->db->prepare("
                UPDATE propostas 
                SET status = 'rejeitada' 
                WHERE freelance_id = ? AND id != ? AND status = 'pendente'
            ");
            $stmt->execute([$proposta['freelance_id'], $propostaId]);
        }

        // Criar notificação
        require_once __DIR__ . '/../Services/NotificationService.php';
        $notificationService = new NotificationService($this->db);
        
        // Buscar user_id da academia
        $stmt = $this->db->prepare("SELECT user_id FROM academias WHERE id = ?");
        $stmt->execute([$academiaId]);
        $academia = $stmt->fetch();
        $academiaNome = $academia ? 'Academia' : 'Academia';

        if ($acao === 'aceita') {
            $notificationService->notifyPropostaAceita(
                $proposta['professor_user_id'],
                $academiaNome,
                $proposta['freelance_titulo'],
                $propostaId
            );
        } else {
            $notificationService->notifyPropostaRejeitada(
                $proposta['professor_user_id'],
                $academiaNome,
                $proposta['freelance_titulo']
            );
        }

        return ['success' => true, 'message' => "Proposta {$acao} com sucesso!"];
    }
}


<?php
// ============================================
// src/Services/NotificationService.php
// ============================================
class NotificationService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($userId, $tipo, $titulo, $mensagem, $link = null) {
        $stmt = $this->db->prepare("
            INSERT INTO notificacoes (user_id, tipo, titulo, mensagem, link) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $tipo, $titulo, $mensagem, $link]);
    }

    public function getByUserId($userId, $limit = 10, $unreadOnly = false) {
        $sql = "SELECT * FROM notificacoes WHERE user_id = ?";
        
        if ($unreadOnly) {
            $sql .= " AND lida = FALSE";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE notificacoes SET lida = TRUE WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare("UPDATE notificacoes SET lida = TRUE WHERE user_id = ? AND lida = FALSE");
        return $stmt->execute([$userId]);
    }

    public function countUnread($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notificacoes WHERE user_id = ? AND lida = FALSE");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function notifyPropostaRecebida($academiaUserId, $professorNome, $freelanceTitulo, $propostaId) {
        return $this->create(
            $academiaUserId,
            'proposta',
            'Nova proposta recebida',
            "O professor {$professorNome} enviou uma proposta para o freelance: {$freelanceTitulo}",
            "index.php?page=propostas&id={$propostaId}"
        );
    }

    public function notifyPropostaAceita($professorUserId, $academiaNome, $freelanceTitulo, $propostaId) {
        return $this->create(
            $professorUserId,
            'aceite',
            'Proposta aceita!',
            "Sua proposta para o freelance '{$freelanceTitulo}' da academia {$academiaNome} foi aceita!",
            "index.php?page=propostas&id={$propostaId}"
        );
    }

    public function notifyPropostaRejeitada($professorUserId, $academiaNome, $freelanceTitulo) {
        return $this->create(
            $professorUserId,
            'rejeicao',
            'Proposta rejeitada',
            "Sua proposta para o freelance '{$freelanceTitulo}' da academia {$academiaNome} foi rejeitada.",
            "index.php?page=propostas"
        );
    }
}


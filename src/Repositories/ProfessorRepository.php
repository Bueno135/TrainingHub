<?php
// ============================================
// src/Repositories/ProfessorRepository.php
// ============================================
class ProfessorRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM professores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM professores WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function create($userId) {
        $stmt = $this->db->prepare("INSERT INTO professores (user_id) VALUES (?)");
        $stmt->execute([$userId]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        $sql = "UPDATE professores SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function updateByUserId($userId, $data) {
        $professor = $this->findByUserId($userId);
        if ($professor) {
            return $this->update($professor['id'], $data);
        }
        return false;
    }

    public function search($filters = []) {
        $sql = "SELECT p.*, u.email, u.status as user_status 
                FROM professores p 
                INNER JOIN users u ON p.user_id = u.id 
                WHERE u.status = 'ativo'";
        $params = [];

        if (!empty($filters['cidade'])) {
            $sql .= " AND p.cidade LIKE ?";
            $params[] = "%{$filters['cidade']}%";
        }

        if (!empty($filters['estado'])) {
            $sql .= " AND p.estado = ?";
            $params[] = $filters['estado'];
        }

        if (!empty($filters['especialidade'])) {
            $sql .= " AND p.especialidades LIKE ?";
            $params[] = "%{$filters['especialidade']}%";
        }

        if (!empty($filters['valor_max'])) {
            $sql .= " AND (p.valor_hora IS NULL OR p.valor_hora <= ?)";
            $params[] = $filters['valor_max'];
        }

        $sql .= " ORDER BY p.nota_media DESC, p.total_avaliacoes DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateNotaMedia($id, $notaMedia, $totalAvaliacoes) {
        $stmt = $this->db->prepare("UPDATE professores SET nota_media = ?, total_avaliacoes = ? WHERE id = ?");
        return $stmt->execute([$notaMedia, $totalAvaliacoes, $id]);
    }
}


<?php
// ============================================
// src/Repositories/FreelanceRepository.php
// ============================================
class FreelanceRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT f.*, a.nome as academia_nome, a.cidade as academia_cidade, a.estado as academia_estado
            FROM freelances f
            INNER JOIN academias a ON f.academia_id = a.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByAcademiaId($academiaId) {
        $stmt = $this->db->prepare("SELECT * FROM freelances WHERE academia_id = ? ORDER BY created_at DESC");
        $stmt->execute([$academiaId]);
        return $stmt->fetchAll();
    }

    public function findAll($filters = []) {
        $sql = "SELECT f.*, a.nome as academia_nome, a.cidade as academia_cidade, a.estado as academia_estado
                FROM freelances f
                INNER JOIN academias a ON f.academia_id = a.id
                WHERE f.status = 'aberto'";
        $params = [];

        if (!empty($filters['cidade'])) {
            $sql .= " AND a.cidade LIKE ?";
            $params[] = "%{$filters['cidade']}%";
        }

        if (!empty($filters['estado'])) {
            $sql .= " AND a.estado = ?";
            $params[] = $filters['estado'];
        }

        if (!empty($filters['tipo_trabalho'])) {
            $sql .= " AND f.tipo_trabalho = ?";
            $params[] = $filters['tipo_trabalho'];
        }

        if (!empty($filters['valor_max'])) {
            $sql .= " AND (f.valor_hora IS NULL OR f.valor_hora <= ?)";
            $params[] = $filters['valor_max'];
        }

        $sql .= " ORDER BY f.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $values = array_values($data);
        
        $sql = "INSERT INTO freelances (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
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
        $sql = "UPDATE freelances SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE freelances SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM freelances WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByAcademia($academiaId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM freelances WHERE academia_id = ?");
        $stmt->execute([$academiaId]);
        return $stmt->fetchColumn();
    }

    public function countAbertosByAcademia($academiaId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM freelances WHERE academia_id = ? AND status = 'aberto'");
        $stmt->execute([$academiaId]);
        return $stmt->fetchColumn();
    }
}


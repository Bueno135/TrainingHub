<?php
// ============================================
// src/Models/User.php
// ============================================
class User {
    private $id;
    private $email;
    private $passwordHash;
    private $tipo;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->email = $data['email'] ?? null;
            $this->passwordHash = $data['password_hash'] ?? null;
            $this->tipo = $data['tipo'] ?? null;
            $this->status = $data['status'] ?? 'ativo';
            $this->createdAt = $data['created_at'] ?? null;
            $this->updatedAt = $data['updated_at'] ?? null;
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function getPasswordHash() { return $this->passwordHash; }
    public function getTipo() { return $this->tipo; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setEmail($email) { $this->email = $email; }
    public function setPasswordHash($hash) { $this->passwordHash = $hash; }
    public function setTipo($tipo) { $this->tipo = $tipo; }
    public function setStatus($status) { $this->status = $status; }

    // Métodos auxiliares
    public function isProfessor() {
        return $this->tipo === 'professor';
    }

    public function isAcademia() {
        return $this->tipo === 'academia';
    }

    public function isAtivo() {
        return $this->status === 'ativo';
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'tipo' => $this->tipo,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}


<?php
// ============================================
// src/Models/Academia.php
// ============================================
class Academia {
    private $id;
    private $userId;
    private $nome;
    private $telefone;
    private $cnpj;
    private $descricao;
    private $cidade;
    private $estado;
    private $endereco;
    private $foto;
    private $website;
    private $createdAt;
    private $updatedAt;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->userId = $data['user_id'] ?? null;
            $this->nome = $data['nome'] ?? null;
            $this->telefone = $data['telefone'] ?? null;
            $this->cnpj = $data['cnpj'] ?? null;
            $this->descricao = $data['descricao'] ?? null;
            $this->cidade = $data['cidade'] ?? null;
            $this->estado = $data['estado'] ?? null;
            $this->endereco = $data['endereco'] ?? null;
            $this->foto = $data['foto'] ?? null;
            $this->website = $data['website'] ?? null;
            $this->createdAt = $data['created_at'] ?? null;
            $this->updatedAt = $data['updated_at'] ?? null;
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getNome() { return $this->nome; }
    public function getTelefone() { return $this->telefone; }
    public function getCnpj() { return $this->cnpj; }
    public function getDescricao() { return $this->descricao; }
    public function getCidade() { return $this->cidade; }
    public function getEstado() { return $this->estado; }
    public function getEndereco() { return $this->endereco; }
    public function getFoto() { return $this->foto; }
    public function getWebsite() { return $this->website; }

    // Setters
    public function setNome($nome) { $this->nome = $nome; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }
    public function setCnpj($cnpj) { $this->cnpj = $cnpj; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    public function setCidade($cidade) { $this->cidade = $cidade; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setEndereco($endereco) { $this->endereco = $endereco; }
    public function setFoto($foto) { $this->foto = $foto; }
    public function setWebsite($website) { $this->website = $website; }

    public function toArray() {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'nome' => $this->nome,
            'telefone' => $this->telefone,
            'cnpj' => $this->cnpj,
            'descricao' => $this->descricao,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'endereco' => $this->endereco,
            'foto' => $this->foto,
            'website' => $this->website
        ];
    }
}


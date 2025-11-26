<?php
// ============================================
// src/Models/Professor.php
// ============================================
class Professor {
    private $id;
    private $userId;
    private $nome;
    private $telefone;
    private $cpf;
    private $cref;
    private $especialidades;
    private $experiencia;
    private $formacao;
    private $disponibilidade;
    private $valorHora;
    private $notaMedia;
    private $totalAvaliacoes;
    private $foto;
    private $cidade;
    private $estado;
    private $endereco;
    private $createdAt;
    private $updatedAt;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->userId = $data['user_id'] ?? null;
            $this->nome = $data['nome'] ?? null;
            $this->telefone = $data['telefone'] ?? null;
            $this->cpf = $data['cpf'] ?? null;
            $this->cref = $data['cref'] ?? null;
            $this->especialidades = $data['especialidades'] ?? null;
            $this->experiencia = $data['experiencia'] ?? null;
            $this->formacao = $data['formacao'] ?? null;
            $this->disponibilidade = $data['disponibilidade'] ?? null;
            $this->valorHora = $data['valor_hora'] ?? null;
            $this->notaMedia = $data['nota_media'] ?? 5.0;
            $this->totalAvaliacoes = $data['total_avaliacoes'] ?? 0;
            $this->foto = $data['foto'] ?? null;
            $this->cidade = $data['cidade'] ?? null;
            $this->estado = $data['estado'] ?? null;
            $this->endereco = $data['endereco'] ?? null;
            $this->createdAt = $data['created_at'] ?? null;
            $this->updatedAt = $data['updated_at'] ?? null;
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getNome() { return $this->nome; }
    public function getTelefone() { return $this->telefone; }
    public function getCpf() { return $this->cpf; }
    public function getCref() { return $this->cref; }
    public function getEspecialidades() { return $this->especialidades; }
    public function getExperiencia() { return $this->experiencia; }
    public function getFormacao() { return $this->formacao; }
    public function getDisponibilidade() { return $this->disponibilidade; }
    public function getValorHora() { return $this->valorHora; }
    public function getNotaMedia() { return $this->notaMedia; }
    public function getTotalAvaliacoes() { return $this->totalAvaliacoes; }
    public function getFoto() { return $this->foto; }
    public function getCidade() { return $this->cidade; }
    public function getEstado() { return $this->estado; }
    public function getEndereco() { return $this->endereco; }

    // Setters
    public function setNome($nome) { $this->nome = $nome; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }
    public function setCpf($cpf) { $this->cpf = $cpf; }
    public function setCref($cref) { $this->cref = $cref; }
    public function setEspecialidades($especialidades) { $this->especialidades = $especialidades; }
    public function setExperiencia($experiencia) { $this->experiencia = $experiencia; }
    public function setFormacao($formacao) { $this->formacao = $formacao; }
    public function setDisponibilidade($disponibilidade) { $this->disponibilidade = $disponibilidade; }
    public function setValorHora($valor) { $this->valorHora = $valor; }
    public function setFoto($foto) { $this->foto = $foto; }
    public function setCidade($cidade) { $this->cidade = $cidade; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setEndereco($endereco) { $this->endereco = $endereco; }

    public function toArray() {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'nome' => $this->nome,
            'telefone' => $this->telefone,
            'cpf' => $this->cpf,
            'cref' => $this->cref,
            'especialidades' => $this->especialidades,
            'experiencia' => $this->experiencia,
            'formacao' => $this->formacao,
            'disponibilidade' => $this->disponibilidade,
            'valor_hora' => $this->valorHora,
            'nota_media' => $this->notaMedia,
            'total_avaliacoes' => $this->totalAvaliacoes,
            'foto' => $this->foto,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'endereco' => $this->endereco
        ];
    }
}

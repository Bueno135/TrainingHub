<?php
// ============================================
// src/Models/Freelance.php
// ============================================
class Freelance {
    private $id;
    private $academiaId;
    private $titulo;
    private $descricao;
    private $tipoTrabalho;
    private $cargaHorariaSemanal;
    private $valorHora;
    private $valorTotal;
    private $requisitos;
    private $beneficios;
    private $status;
    private $dataInicio;
    private $dataFim;
    private $createdAt;
    private $updatedAt;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->academiaId = $data['academia_id'] ?? null;
            $this->titulo = $data['titulo'] ?? null;
            $this->descricao = $data['descricao'] ?? null;
            $this->tipoTrabalho = $data['tipo_trabalho'] ?? 'presencial';
            $this->cargaHorariaSemanal = $data['carga_horaria_semanal'] ?? null;
            $this->valorHora = $data['valor_hora'] ?? null;
            $this->valorTotal = $data['valor_total'] ?? null;
            $this->requisitos = $data['requisitos'] ?? null;
            $this->beneficios = $data['beneficios'] ?? null;
            $this->status = $data['status'] ?? 'aberto';
            $this->dataInicio = $data['data_inicio'] ?? null;
            $this->dataFim = $data['data_fim'] ?? null;
            $this->createdAt = $data['created_at'] ?? null;
            $this->updatedAt = $data['updated_at'] ?? null;
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getAcademiaId() { return $this->academiaId; }
    public function getTitulo() { return $this->titulo; }
    public function getDescricao() { return $this->descricao; }
    public function getTipoTrabalho() { return $this->tipoTrabalho; }
    public function getCargaHorariaSemanal() { return $this->cargaHorariaSemanal; }
    public function getValorHora() { return $this->valorHora; }
    public function getValorTotal() { return $this->valorTotal; }
    public function getRequisitos() { return $this->requisitos; }
    public function getBeneficios() { return $this->beneficios; }
    public function getStatus() { return $this->status; }
    public function getDataInicio() { return $this->dataInicio; }
    public function getDataFim() { return $this->dataFim; }

    // Setters
    public function setTitulo($titulo) { $this->titulo = $titulo; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    public function setTipoTrabalho($tipo) { $this->tipoTrabalho = $tipo; }
    public function setCargaHorariaSemanal($horas) { $this->cargaHorariaSemanal = $horas; }
    public function setValorHora($valor) { $this->valorHora = $valor; }
    public function setValorTotal($valor) { $this->valorTotal = $valor; }
    public function setRequisitos($requisitos) { $this->requisitos = $requisitos; }
    public function setBeneficios($beneficios) { $this->beneficios = $beneficios; }
    public function setStatus($status) { $this->status = $status; }
    public function setDataInicio($data) { $this->dataInicio = $data; }
    public function setDataFim($data) { $this->dataFim = $data; }

    public function isAberto() {
        return $this->status === 'aberto';
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'academia_id' => $this->academiaId,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'tipo_trabalho' => $this->tipoTrabalho,
            'carga_horaria_semanal' => $this->cargaHorariaSemanal,
            'valor_hora' => $this->valorHora,
            'valor_total' => $this->valorTotal,
            'requisitos' => $this->requisitos,
            'beneficios' => $this->beneficios,
            'status' => $this->status,
            'data_inicio' => $this->dataInicio,
            'data_fim' => $this->dataFim
        ];
    }
}


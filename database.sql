-- ============================================
-- TrainingHub - Script de Criação do Banco de Dados
-- ============================================

CREATE DATABASE IF NOT EXISTS traininghub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE traininghub;

-- ============================================
-- TABELA: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    tipo ENUM('professor', 'academia') NOT NULL,
    status ENUM('ativo', 'inativo', 'suspenso') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_tipo (tipo),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: professores
-- ============================================
CREATE TABLE IF NOT EXISTS professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    nome VARCHAR(255),
    telefone VARCHAR(20),
    cpf VARCHAR(14),
    cref VARCHAR(20),
    especialidades TEXT,
    experiencia TEXT,
    formacao TEXT,
    disponibilidade TEXT,
    valor_hora DECIMAL(10, 2),
    nota_media DECIMAL(3, 2) DEFAULT 5.0,
    total_avaliacoes INT DEFAULT 0,
    foto VARCHAR(255),
    cidade VARCHAR(100),
    estado VARCHAR(2),
    endereco TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_cidade (cidade),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: academias
-- ============================================
CREATE TABLE IF NOT EXISTS academias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    nome VARCHAR(255),
    telefone VARCHAR(20),
    cnpj VARCHAR(18),
    descricao TEXT,
    cidade VARCHAR(100),
    estado VARCHAR(2),
    endereco TEXT,
    foto VARCHAR(255),
    website VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_cidade (cidade),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: freelances
-- ============================================
CREATE TABLE IF NOT EXISTS freelances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    academia_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    tipo_trabalho ENUM('presencial', 'online', 'hibrido') DEFAULT 'presencial',
    carga_horaria_semanal INT,
    valor_hora DECIMAL(10, 2),
    valor_total DECIMAL(10, 2),
    requisitos TEXT,
    beneficios TEXT,
    status ENUM('aberto', 'em_analise', 'fechado', 'cancelado') DEFAULT 'aberto',
    data_inicio DATE,
    data_fim DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (academia_id) REFERENCES academias(id) ON DELETE CASCADE,
    INDEX idx_academia_id (academia_id),
    INDEX idx_status (status),
    INDEX idx_tipo_trabalho (tipo_trabalho)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: propostas
-- ============================================
CREATE TABLE IF NOT EXISTS propostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    freelance_id INT NOT NULL,
    professor_id INT NOT NULL,
    mensagem TEXT,
    valor_proposto DECIMAL(10, 2),
    status ENUM('pendente', 'aceita', 'rejeitada', 'cancelada') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (freelance_id) REFERENCES freelances(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES professores(id) ON DELETE CASCADE,
    INDEX idx_freelance_id (freelance_id),
    INDEX idx_professor_id (professor_id),
    INDEX idx_status (status),
    UNIQUE KEY unique_proposta (freelance_id, professor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: sessoes
-- ============================================
CREATE TABLE IF NOT EXISTS sessoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proposta_id INT NOT NULL,
    data_sessao DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME,
    status ENUM('agendada', 'em_andamento', 'concluida', 'cancelada') DEFAULT 'agendada',
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proposta_id) REFERENCES propostas(id) ON DELETE CASCADE,
    INDEX idx_proposta_id (proposta_id),
    INDEX idx_data_sessao (data_sessao),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: avaliacoes
-- ============================================
CREATE TABLE IF NOT EXISTS avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sessao_id INT NOT NULL,
    avaliador_id INT NOT NULL,
    avaliado_id INT NOT NULL,
    tipo_avaliador ENUM('professor', 'academia') NOT NULL,
    nota INT NOT NULL CHECK (nota >= 1 AND nota <= 5),
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sessao_id) REFERENCES sessoes(id) ON DELETE CASCADE,
    INDEX idx_sessao_id (sessao_id),
    INDEX idx_avaliador_id (avaliador_id),
    INDEX idx_avaliado_id (avaliado_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: notificacoes
-- ============================================
CREATE TABLE IF NOT EXISTS notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipo ENUM('proposta', 'aceite', 'rejeicao', 'mensagem', 'sistema') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT,
    link VARCHAR(255),
    lida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_lida (lida),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: mensagens
-- ============================================
CREATE TABLE IF NOT EXISTS mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proposta_id INT NOT NULL,
    remetente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proposta_id) REFERENCES propostas(id) ON DELETE CASCADE,
    INDEX idx_proposta_id (proposta_id),
    INDEX idx_remetente_id (remetente_id),
    INDEX idx_destinatario_id (destinatario_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - TrainingHub</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .profile-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
    </style>
</head>
<body>
    <?php
    if (!isset($user)) {
        header('Location: index.php?page=login');
        exit;
    }

    // Buscar perfil
    if ($user['tipo'] === 'professor') {
        require_once __DIR__ . '/../../Controller/ProfessorController.php';
        $controller = new ProfessorController($db);
        $perfil = $controller->getProfile($user['id']);
    } else {
        require_once __DIR__ . '/../../Controller/AcademiaController.php';
        $controller = new AcademiaController($db);
        $perfil = $controller->getProfile($user['id']);
    }
    ?>

    <nav class="navbar">
        <div class="navbar-brand">TrainingHub</div>
        <div class="navbar-user">
            <a href="index.php?page=dashboard" class="logout-btn" style="margin-right: 10px;">Dashboard</a>
            <a href="index.php?page=logout" class="logout-btn">Sair</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-banner">
            <h1>Meu Perfil</h1>
            <p>Complete suas informações para melhorar sua visibilidade</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Perfil atualizado com sucesso!</div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="profile-form">
            <form method="POST" action="index.php?page=perfil">
                <?php if ($user['tipo'] === 'professor'): ?>
                    <div class="form-group">
                        <label for="nome">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($perfil['nome'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($perfil['telefone'] ?? ''); ?>" placeholder="(00) 00000-0000">
                        </div>
                        <div class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($perfil['cpf'] ?? ''); ?>" placeholder="000.000.000-00">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cref">CREF</label>
                            <input type="text" id="cref" name="cref" value="<?php echo htmlspecialchars($perfil['cref'] ?? ''); ?>" placeholder="000000-G/UF">
                        </div>
                        <div class="form-group">
                            <label for="valor_hora">Valor por Hora (R$)</label>
                            <input type="number" id="valor_hora" name="valor_hora" step="0.01" value="<?php echo htmlspecialchars($perfil['valor_hora'] ?? ''); ?>" placeholder="0.00">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="especialidades">Especialidades</label>
                        <textarea id="especialidades" name="especialidades" placeholder="Ex: Musculação, Pilates, Crossfit..."><?php echo htmlspecialchars($perfil['especialidades'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="formacao">Formação</label>
                        <textarea id="formacao" name="formacao" placeholder="Descreva sua formação acadêmica"><?php echo htmlspecialchars($perfil['formacao'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="experiencia">Experiência</label>
                        <textarea id="experiencia" name="experiencia" placeholder="Descreva sua experiência profissional"><?php echo htmlspecialchars($perfil['experiencia'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="disponibilidade">Disponibilidade</label>
                        <textarea id="disponibilidade" name="disponibilidade" placeholder="Ex: Segunda a Sexta, 8h às 18h"><?php echo htmlspecialchars($perfil['disponibilidade'] ?? ''); ?></textarea>
                    </div>

                <?php else: ?>
                    <div class="form-group">
                        <label for="nome">Nome da Academia *</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($perfil['nome'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($perfil['telefone'] ?? ''); ?>" placeholder="(00) 00000-0000">
                        </div>
                        <div class="form-group">
                            <label for="cnpj">CNPJ</label>
                            <input type="text" id="cnpj" name="cnpj" value="<?php echo htmlspecialchars($perfil['cnpj'] ?? ''); ?>" placeholder="00.000.000/0000-00">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" placeholder="Descreva sua academia"><?php echo htmlspecialchars($perfil['descricao'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($perfil['website'] ?? ''); ?>" placeholder="https://exemplo.com">
                    </div>
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($perfil['cidade'] ?? ''); ?>" placeholder="São Paulo">
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <input type="text" id="estado" name="estado" maxlength="2" value="<?php echo htmlspecialchars($perfil['estado'] ?? ''); ?>" placeholder="SP">
                    </div>
                </div>

                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <textarea id="endereco" name="endereco" placeholder="Rua, número, bairro"><?php echo htmlspecialchars($perfil['endereco'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn-submit">Salvar Perfil</button>
            </form>
        </div>
    </div>
</body>
</html>


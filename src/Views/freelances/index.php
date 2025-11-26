<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelances - TrainingHub</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .freelance-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .freelance-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .freelance-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        .freelance-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .freelance-meta {
            color: #999;
            font-size: 14px;
        }
        .freelance-status {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-aberto {
            background: #efe;
            color: #3c3;
        }
        .status-fechado {
            background: #fee;
            color: #c33;
        }
        .freelance-body {
            margin-bottom: 15px;
        }
        .freelance-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }
        .btn-action {
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-danger {
            background: #f44;
            color: white;
        }
        .create-form {
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
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body>
    <?php
    if (!isset($user)) {
        header('Location: index.php?page=login');
        exit;
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
            <h1><?php echo $user['tipo'] === 'academia' ? 'Meus Freelances' : 'Buscar Freelances'; ?></h1>
            <p><?php echo $user['tipo'] === 'academia' ? 'Gerencie seus freelances publicados' : 'Encontre oportunidades de trabalho'; ?></p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" style="background: #efe; color: #3c3; padding: 12px; border-radius: 8px; margin-bottom: 20px;">Operação realizada com sucesso!</div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error" style="background: #fee; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 20px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($user['tipo'] === 'academia' && $action === 'criar'): ?>
            <div class="create-form">
                <h2 style="margin-bottom: 20px;">Publicar Novo Freelance</h2>
                <form method="POST" action="index.php?page=freelances&action=criar">
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" required placeholder="Ex: Professor de Musculação">
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição *</label>
                        <textarea id="descricao" name="descricao" required rows="5" placeholder="Descreva o freelance..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tipo_trabalho">Tipo de Trabalho</label>
                        <select id="tipo_trabalho" name="tipo_trabalho">
                            <option value="presencial">Presencial</option>
                            <option value="online">Online</option>
                            <option value="hibrido">Híbrido</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="carga_horaria_semanal">Carga Horária Semanal (horas)</label>
                        <input type="number" id="carga_horaria_semanal" name="carga_horaria_semanal" min="1">
                    </div>

                    <div class="form-group">
                        <label for="valor_hora">Valor por Hora (R$)</label>
                        <input type="number" id="valor_hora" name="valor_hora" step="0.01" min="0">
                    </div>

                    <div class="form-group">
                        <label for="requisitos">Requisitos</label>
                        <textarea id="requisitos" name="requisitos" rows="3" placeholder="Ex: CREF ativo, experiência mínima de 2 anos..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="beneficios">Benefícios</label>
                        <textarea id="beneficios" name="beneficios" rows="3" placeholder="Ex: Vale refeição, plano de saúde..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 14px 30px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">Publicar Freelance</button>
                    <a href="index.php?page=freelances" class="btn-action" style="margin-left: 10px; background: #e0e0e0; color: #333;">Cancelar</a>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($user['tipo'] === 'academia' && $action !== 'criar'): ?>
            <div style="margin-bottom: 20px;">
                <a href="index.php?page=freelances&action=criar" class="btn-action btn-primary">➕ Publicar Novo Freelance</a>
            </div>
        <?php endif; ?>

        <?php if (empty($freelances)): ?>
            <div class="empty-state" style="text-align: center; padding: 40px; background: white; border-radius: 15px;">
                <div class="icon" style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;">📭</div>
                <p>Nenhum freelance encontrado</p>
            </div>
        <?php else: ?>
            <?php foreach ($freelances as $freelance): ?>
                <div class="freelance-card">
                    <div class="freelance-header">
                        <div>
                            <div class="freelance-title"><?php echo htmlspecialchars($freelance['titulo']); ?></div>
                            <div class="freelance-meta">
                                <?php if ($user['tipo'] === 'professor'): ?>
                                    <?php echo htmlspecialchars($freelance['academia_nome'] ?? 'Academia'); ?> - 
                                    <?php echo htmlspecialchars($freelance['academia_cidade'] ?? ''); ?>/<?php echo htmlspecialchars($freelance['academia_estado'] ?? ''); ?>
                                <?php else: ?>
                                    <?php echo ucfirst($freelance['tipo_trabalho']); ?> - 
                                    <?php echo $freelance['carga_horaria_semanal'] ? $freelance['carga_horaria_semanal'] . 'h/semana' : ''; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="freelance-status status-<?php echo $freelance['status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $freelance['status'])); ?>
                        </span>
                    </div>

                    <div class="freelance-body">
                        <p><?php echo nl2br(htmlspecialchars($freelance['descricao'])); ?></p>
                        <?php if ($freelance['valor_hora']): ?>
                            <p style="margin-top: 10px;"><strong>Valor:</strong> R$ <?php echo number_format($freelance['valor_hora'], 2, ',', '.'); ?>/hora</p>
                        <?php endif; ?>
                    </div>

                    <div class="freelance-footer">
                        <div>
                            <small style="color: #999;">Publicado em <?php echo date('d/m/Y', strtotime($freelance['created_at'])); ?></small>
                        </div>
                        <div>
                            <?php if ($user['tipo'] === 'professor' && $freelance['status'] === 'aberto'): ?>
                                <a href="index.php?page=propostas&action=criar&freelance_id=<?php echo $freelance['id']; ?>" class="btn-action btn-primary">Enviar Proposta</a>
                            <?php elseif ($user['tipo'] === 'academia'): ?>
                                <a href="index.php?page=propostas&freelance_id=<?php echo $freelance['id']; ?>" class="btn-action btn-primary">Ver Propostas</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>


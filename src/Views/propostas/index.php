<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propostas - TrainingHub</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .proposta-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
        .proposta-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        .proposta-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .proposta-status {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }
        .status-aceita {
            background: #efe;
            color: #3c3;
        }
        .status-rejeitada {
            background: #fee;
            color: #c33;
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
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn-action {
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            display: inline-block;
        }
        .btn-success {
            background: #3c3;
            color: white;
        }
        .btn-danger {
            background: #c33;
            color: white;
        }
        .btn-success:hover {
            background: #2a2;
        }
        .btn-danger:hover {
            background: #a22;
        }
    </style>
</head>
<body>
    <?php
    if (!isset($user)) {
        header('Location: index.php?page=login');
        exit;
    }

    require_once __DIR__ . '/../../Controller/FreelanceController.php';
    $freelanceController = new FreelanceController($db);

    // Buscar propostas
    if ($user['tipo'] === 'professor') {
        require_once __DIR__ . '/../../Controller/ProfessorController.php';
        $controller = new ProfessorController($db);
        $stmt = $db->prepare("SELECT id FROM professores WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $professor = $stmt->fetch();
        $propostas = $professor ? $controller->getPropostas($professor['id']) : [];
    } else {
        $stmt = $db->prepare("SELECT id FROM academias WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $academia = $stmt->fetch();
        
        if (isset($_GET['freelance_id'])) {
            $propostas = $academia ? $freelanceController->getPropostas($_GET['freelance_id'], $academia['id']) : [];
        } else {
            // Buscar todas as propostas de todos os freelances da academia
            $stmt = $db->prepare("
                SELECT p.*, f.titulo as freelance_titulo, pr.nome as professor_nome, pr.cref, pr.nota_media
                FROM propostas p
                INNER JOIN freelances f ON p.freelance_id = f.id
                INNER JOIN academias a ON f.academia_id = a.id
                INNER JOIN professores pr ON p.professor_id = pr.id
                WHERE a.user_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$user['id']]);
            $propostas = $stmt->fetchAll();
        }
    }

    // Buscar freelance para criar proposta
    $freelanceParaProposta = null;
    if (isset($_GET['freelance_id']) && $user['tipo'] === 'professor') {
        $freelanceParaProposta = $freelanceController->getById($_GET['freelance_id']);
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
            <h1><?php echo $user['tipo'] === 'professor' ? 'Minhas Propostas' : 'Propostas Recebidas'; ?></h1>
            <p><?php echo $user['tipo'] === 'professor' ? 'Acompanhe suas propostas enviadas' : 'Gerencie as propostas dos professores'; ?></p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" style="background: #efe; color: #3c3; padding: 12px; border-radius: 8px; margin-bottom: 20px;">Operação realizada com sucesso!</div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error" style="background: #fee; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 20px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($freelanceParaProposta && $action === 'criar'): ?>
            <div class="create-form">
                <h2 style="margin-bottom: 20px;">Enviar Proposta para: <?php echo htmlspecialchars($freelanceParaProposta['titulo']); ?></h2>
                <form method="POST" action="index.php?page=propostas&action=criar">
                    <input type="hidden" name="freelance_id" value="<?php echo $freelanceParaProposta['id']; ?>">
                    
                    <div class="form-group">
                        <label for="mensagem">Mensagem</label>
                        <textarea id="mensagem" name="mensagem" rows="5" placeholder="Descreva por que você é a pessoa ideal para este freelance..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="valor_proposto">Valor Proposto (R$)</label>
                        <input type="number" id="valor_proposto" name="valor_proposto" step="0.01" min="0" placeholder="0.00">
                        <small style="color: #999;">Deixe em branco para usar o valor do freelance</small>
                    </div>

                    <button type="submit" class="btn-action btn-success">Enviar Proposta</button>
                    <a href="index.php?page=freelances" class="btn-action" style="margin-left: 10px; background: #e0e0e0; color: #333;">Cancelar</a>
                </form>
            </div>
        <?php endif; ?>

        <?php if (empty($propostas) && !$freelanceParaProposta): ?>
            <div class="empty-state" style="text-align: center; padding: 40px; background: white; border-radius: 15px;">
                <div class="icon" style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;">📭</div>
                <p>Nenhuma proposta encontrada</p>
            </div>
        <?php else: ?>
            <?php foreach ($propostas as $proposta): ?>
                <div class="proposta-card">
                    <div class="proposta-header">
                        <div>
                            <div class="proposta-title">
                                <?php if ($user['tipo'] === 'professor'): ?>
                                    Freelance: <?php echo htmlspecialchars($proposta['freelance_titulo']); ?>
                                <?php else: ?>
                                    Professor: <?php echo htmlspecialchars($proposta['professor_nome'] ?? 'Professor'); ?>
                                    <?php if ($proposta['cref']): ?>
                                        - CREF: <?php echo htmlspecialchars($proposta['cref']); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div style="color: #999; font-size: 14px; margin-top: 5px;">
                                Enviada em <?php echo date('d/m/Y H:i', strtotime($proposta['created_at'])); ?>
                            </div>
                        </div>
                        <span class="proposta-status status-<?php echo $proposta['status']; ?>">
                            <?php echo ucfirst($proposta['status']); ?>
                        </span>
                    </div>

                    <?php if ($proposta['mensagem']): ?>
                        <div style="margin-bottom: 15px; padding: 15px; background: #f9f9f9; border-radius: 8px;">
                            <p><?php echo nl2br(htmlspecialchars($proposta['mensagem'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #f0f0f0;">
                        <div>
                            <?php if ($proposta['valor_proposto']): ?>
                                <strong>Valor Proposto:</strong> R$ <?php echo number_format($proposta['valor_proposto'], 2, ',', '.'); ?>
                            <?php endif; ?>
                            <?php if ($user['tipo'] === 'academia' && $proposta['nota_media']): ?>
                                <span style="margin-left: 15px;">
                                    <strong>Avaliação:</strong> ⭐ <?php echo number_format($proposta['nota_media'], 1); ?>/5.0
                                </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if ($user['tipo'] === 'academia' && $proposta['status'] === 'pendente'): ?>
                                <form method="POST" action="index.php?page=propostas&action=responder" style="display: inline;">
                                    <input type="hidden" name="proposta_id" value="<?php echo $proposta['id']; ?>">
                                    <button type="submit" name="acao" value="aceita" class="btn-action btn-success">Aceitar</button>
                                    <button type="submit" name="acao" value="rejeitada" class="btn-action btn-danger" style="margin-left: 10px;">Rejeitar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>


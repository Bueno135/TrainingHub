<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TrainingHub</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php
    // Verificar autenticação
    if (!isset($user)) {
        header('Location: index.php?page=login');
        exit;
    }

    // Buscar dados do usuário
    if ($user['tipo'] === 'professor') {
        $stmt = $db->prepare("SELECT * FROM professores WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $perfil = $stmt->fetch();
        $tipoBadge = 'Professor';
        $tipoIcon = '💪';
    } else {
        $stmt = $db->prepare("SELECT * FROM academias WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $perfil = $stmt->fetch();
        $tipoBadge = 'Academia';
        $tipoIcon = '🏢';
    }

    $nomeUsuario = $perfil['nome'] ?? 'Usuário';
    $primeiraLetra = mb_substr($nomeUsuario, 0, 1);
    ?>

    <nav class="navbar">
        <div class="navbar-brand">TrainingHub</div>
        <div class="navbar-user">
            <div class="user-info">
                <div class="user-avatar"><?php echo htmlspecialchars($primeiraLetra); ?></div>
                <div>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($user['email']); ?></div>
                    <div style="font-size: 12px; opacity: 0.8;"><?php echo $tipoBadge; ?></div>
                </div>
            </div>
            <a href="index.php?page=logout" class="logout-btn">Sair</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-banner">
            <h1>Bem-vindo, <?php echo htmlspecialchars($nomeUsuario); ?>! <?php echo $tipoIcon; ?></h1>
            <p>Este é o seu painel de controle do TrainingHub</p>
            <span class="badge">Conta: <?php echo $tipoBadge; ?></span>
        </div>

        <?php if ($user['tipo'] === 'professor'): ?>
        <!-- Dashboard do Professor -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📊</div>
                <div class="value">0</div>
                <div class="label">Propostas Enviadas</div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="value">0</div>
                <div class="label">Sessões Concluídas</div>
            </div>
            <div class="stat-card">
                <div class="icon">⭐</div>
                <div class="value"><?php echo number_format($perfil['nota_media'] ?? 5.0, 1); ?></div>
                <div class="label">Avaliação Média</div>
            </div>
            <div class="stat-card">
                <div class="icon">💰</div>
                <div class="value">R$ 0</div>
                <div class="label">Ganhos Totais</div>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Ações Rápidas</h2>
            <div class="actions-grid">
                <a href="#" class="action-btn">
                    <div class="icon">📝</div>
                    <div class="title">Completar Perfil</div>
                </a>
                <a href="#" class="action-btn">
                    <div class="icon">🔍</div>
                    <div class="title">Buscar Freelances</div>
                </a>
                <a href="#" class="action-btn">
                    <div class="icon">📅</div>
                    <div class="title">Minha Agenda</div>
                </a>
                <a href="#" class="action-btn">
                    <div class="icon">⏰</div>
                    <div class="title">Disponibilidade</div>
                </a>
            </div>
        </div>

        <?php else: ?>
        <!-- Dashboard da Academia -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📢</div>
                <div class="value">0</div>
                <div class="label">Freelances Publicados</div>
            </div>
            <div class="stat-card">
                <div class="icon">👥</div>
                <div class="value">0</div>
                <div class="label">Professores Contratados</div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="value">0</div>
                <div class="label">Sessões Realizadas</div>
            </div>
            <div class="stat-card">
                <div class="icon">💵</div>
                <div class="value">R$ 0</div>
                <div class="label">Total Investido</div>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Ações Rápidas</h2>
            <div class="actions-grid">
                <a href="#" class="action-btn">
                    <div class="icon">📝</div>
                    <div class="title">Completar Perfil</div>
                </a>
                <a href="#" class="action-btn">
                    <div class="icon">➕</div>
                    <div class="title">Publicar Freelance</div>
                </a>
                <a href="#" class="action-btn">
                    <div class="icon">🔍</div>
                    <div class="title">Buscar Professores</div>
                </a>
                <a href="#" class="action-btn">
                    <div class="icon">📊</div>
                    <div class="title">Ver Propostas</div>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <div class="recent-activity">
            <h2>Atividade Recente</h2>
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>Nenhuma atividade recente</p>
                <p style="font-size: 14px; margin-top: 10px;">Complete seu perfil para começar!</p>
            </div>
        </div>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>
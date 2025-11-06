<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TrainingHub</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TrainingHub</h1>
            <p>Conectando professores e academias</p>
        </div>

        <div class="form-container">
            <?php
            // Processar login
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($authService)) {
                require_once __DIR__ . '/../../Controller/AuthController.php';
                $controller = new AuthController($authService);
                $result = $controller->handleLogin();
                
                if ($result && !$result['success']) {
                    echo '<div class="alert alert-error">' . htmlspecialchars($result['message']) . '</div>';
                }
            }
            ?>

            <form method="POST" action="index.php?page=login">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="seu@email.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
                        required
                    >
                </div>

                <button type="submit" class="btn">Entrar</button>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <a href="index.php?page=register" class="link">Criar nova conta</a>
        </div>
    </div>
</body>
</html>
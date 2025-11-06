<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - TrainingHub</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Criar Conta</h1>
            <p>Junte-se à maior plataforma de conexão</p>
        </div>

        <div class="form-container">
            <?php
            // Processar cadastro
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($authService)) {
                require_once __DIR__ . '/../../Controller/AuthController.php';
                $controller = new AuthController($authService);
                $result = $controller->handleRegister();
                
                if ($result) {
                    if ($result['success']) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($result['message']) . ' <a href="index.php?page=login">Faça login</a></div>';
                    } else {
                        echo '<div class="alert alert-error">' . htmlspecialchars($result['message']) . '</div>';
                    }
                }
            }
            ?>

            <form method="POST" action="index.php?page=register" id="registerForm">
                <div class="form-group">
                    <label>Tipo de Conta</label>
                    <div class="tipo-selector">
                        <div class="tipo-option">
                            <input type="radio" id="tipo-professor" name="tipo" value="professor" required>
                            <label for="tipo-professor">
                                <span class="icon">💪</span>
                                <span class="title">Professor</span>
                            </label>
                        </div>
                        <div class="tipo-option">
                            <input type="radio" id="tipo-academia" name="tipo" value="academia" required>
                            <label for="tipo-academia">
                                <span class="icon">🏢</span>
                                <span class="title">Academia</span>
                            </label>
                        </div>
                    </div>
                </div>

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
                        placeholder="Mínimo 6 caracteres"
                        required
                        minlength="6"
                    >
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Senha</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Digite a senha novamente"
                        required
                    >
                </div>

                <button type="submit" class="btn">Criar Conta</button>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <a href="index.php?page=login" class="link">Já tem uma conta? Faça login</a>
        </div>
    </div>

    <script src="assets/js/register.js"></script>
</body>
</html>
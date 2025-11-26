<?php
// ============================================
// src/Services/EmailService.php
// ============================================
class EmailService {
    private $config;

    public function __construct($config = []) {
        $this->config = $config;
    }

    public function send($to, $subject, $message, $from = null) {
        if ($from === null) {
            $from = $this->config['from_email'] ?? 'noreply@traininghub.com';
        }

        $headers = [
            'From: ' . $from,
            'Reply-To: ' . $from,
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8'
        ];

        $headersString = implode("\r\n", $headers);

        return mail($to, $subject, $message, $headersString);
    }

    public function sendWelcomeEmail($email, $nome, $tipo) {
        $subject = "Bem-vindo ao TrainingHub!";
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>TrainingHub</h1>
                </div>
                <div class='content'>
                    <h2>Olá, " . htmlspecialchars($nome) . "!</h2>
                    <p>Bem-vindo ao TrainingHub, a plataforma que conecta professores de educação física com academias.</p>
                    <p>Seu cadastro como <strong>" . htmlspecialchars($tipo) . "</strong> foi realizado com sucesso!</p>
                    <p>Complete seu perfil para começar a usar a plataforma.</p>
                    <a href='" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/index.php?page=dashboard' class='button'>Acessar Dashboard</a>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->send($email, $subject, $message);
    }

    public function sendPropostaNotification($email, $nome, $freelanceTitulo) {
        $subject = "Nova proposta recebida - TrainingHub";
        $message = "
        <html>
        <body>
            <h2>Olá, " . htmlspecialchars($nome) . "!</h2>
            <p>Você recebeu uma nova proposta para o freelance: <strong>" . htmlspecialchars($freelanceTitulo) . "</strong></p>
            <p>Acesse seu dashboard para ver os detalhes.</p>
        </body>
        </html>
        ";

        return $this->send($email, $subject, $message);
    }

    public function sendPropostaAceita($email, $nome, $freelanceTitulo) {
        $subject = "Proposta aceita - TrainingHub";
        $message = "
        <html>
        <body>
            <h2>Parabéns, " . htmlspecialchars($nome) . "!</h2>
            <p>Sua proposta para o freelance <strong>" . htmlspecialchars($freelanceTitulo) . "</strong> foi aceita!</p>
            <p>Acesse seu dashboard para mais informações.</p>
        </body>
        </html>
        ";

        return $this->send($email, $subject, $message);
    }
}


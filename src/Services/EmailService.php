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

        // Se SMTP estiver habilitado, usar SMTP
        if (!empty($this->config['smtp_enabled']) && $this->config['smtp_enabled'] === true) {
            return $this->sendViaSMTP($to, $subject, $message, $from);
        }

        // Caso contrário, usar mail() nativo
        $fromName = $this->config['from_name'] ?? 'TrainingHub';
        $headers = [
            'From: ' . $fromName . ' <' . $from . '>',
            'Reply-To: ' . $from,
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8'
        ];

        $headersString = implode("\r\n", $headers);

        return mail($to, $subject, $message, $headersString);
    }

    private function sendViaSMTP($to, $subject, $message, $from) {
        $host = $this->config['smtp_host'] ?? 'smtp.gmail.com';
        $port = $this->config['smtp_port'] ?? 587;
        $username = $this->config['smtp_username'] ?? '';
        $password = $this->config['smtp_password'] ?? '';
        $encryption = $this->config['smtp_encryption'] ?? 'tls';
        $fromName = $this->config['from_name'] ?? 'TrainingHub';

        if (empty($username) || empty($password)) {
            error_log("EmailService: SMTP habilitado mas credenciais não configuradas");
            return false;
        }

        try {
            // Criar conexão
            $socket = @fsockopen($host, $port, $errno, $errstr, 30);
            if (!$socket) {
                error_log("EmailService: Erro ao conectar ao SMTP: $errstr ($errno)");
                return false;
            }

            // Ler resposta inicial
            $this->readResponse($socket);

            // EHLO
            fputs($socket, "EHLO " . $host . "\r\n");
            $this->readResponse($socket);

            // STARTTLS se necessário
            if ($encryption === 'tls') {
                fputs($socket, "STARTTLS\r\n");
                $this->readResponse($socket);
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                fputs($socket, "EHLO " . $host . "\r\n");
                $this->readResponse($socket);
            }

            // Autenticação
            fputs($socket, "AUTH LOGIN\r\n");
            $this->readResponse($socket);

            fputs($socket, base64_encode($username) . "\r\n");
            $this->readResponse($socket);

            fputs($socket, base64_encode($password) . "\r\n");
            $authResponse = $this->readResponse($socket);
            if (strpos($authResponse, '235') === false) {
                error_log("EmailService: Falha na autenticação SMTP");
                fclose($socket);
                return false;
            }

            // MAIL FROM
            fputs($socket, "MAIL FROM: <" . $from . ">\r\n");
            $this->readResponse($socket);

            // RCPT TO
            fputs($socket, "RCPT TO: <" . $to . ">\r\n");
            $this->readResponse($socket);

            // DATA
            fputs($socket, "DATA\r\n");
            $this->readResponse($socket);

            // Headers e corpo
            $emailData = "From: " . $fromName . " <" . $from . ">\r\n";
            $emailData .= "To: <" . $to . ">\r\n";
            $emailData .= "Subject: " . $subject . "\r\n";
            $emailData .= "MIME-Version: 1.0\r\n";
            $emailData .= "Content-Type: text/html; charset=UTF-8\r\n";
            $emailData .= "\r\n";
            $emailData .= $message . "\r\n";
            $emailData .= ".\r\n";

            fputs($socket, $emailData);
            $this->readResponse($socket);

            // QUIT
            fputs($socket, "QUIT\r\n");
            fclose($socket);

            return true;
        } catch (Exception $e) {
            error_log("EmailService: Erro ao enviar email via SMTP: " . $e->getMessage());
            if (isset($socket)) {
                fclose($socket);
            }
            return false;
        }
    }

    private function readResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        return $response;
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


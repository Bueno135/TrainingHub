# 📧 Configuração de Email - TrainingHub

## Configuração do Gmail SMTP

Para enviar emails através do Gmail, você precisa configurar uma **Senha de App** (não use sua senha normal do Gmail).

### Passo a Passo:

1. **Ative a Verificação em Duas Etapas**
   - Acesse: https://myaccount.google.com/security
   - Ative a "Verificação em duas etapas" se ainda não estiver ativada

2. **Gere uma Senha de App**
   - Acesse: https://myaccount.google.com/apppasswords
   - Selecione "App" e escolha "Email"
   - Selecione "Outro (nome personalizado)" e digite "TrainingHub"
   - Clique em "Gerar"
   - **Copie a senha gerada** (16 caracteres, sem espaços ou com espaços - ambos funcionam)

3. **Configure o arquivo `.env`**
   ```env
   MAIL_FROM_EMAIL=seuemail@gmail.com
   MAIL_FROM_NAME=TrainingHub
   MAIL_SMTP_ENABLED=true
   MAIL_SMTP_HOST=smtp.gmail.com
   MAIL_SMTP_PORT=587
   MAIL_SMTP_USERNAME=seuemail@gmail.com
   MAIL_SMTP_PASSWORD=sua-senha-de-app-aqui
   MAIL_SMTP_ENCRYPTION=tls
   ```

4. **Importante:**
   - Use a **senha de app** (16 caracteres), não sua senha normal do Gmail
   - A senha de app pode ter espaços ou não - ambos funcionam
   - Mantenha a senha de app segura e não compartilhe

### Exemplo de Senha de App:
```
abcd efgh ijkl mnop
```
ou
```
abcdefghijklmnop
```

Ambos os formatos funcionam no `.env`.

### Testando a Configuração

Após configurar, o sistema tentará enviar emails usando SMTP quando:
- Um novo usuário se cadastra
- Uma proposta é recebida
- Uma proposta é aceita/rejeitada

### Troubleshooting

**Erro: "Falha na autenticação SMTP"**
- Verifique se a senha de app está correta
- Certifique-se de que copiou a senha completa (16 caracteres)
- Verifique se a verificação em duas etapas está ativada

**Erro: "Erro ao conectar ao SMTP"**
- Verifique sua conexão com a internet
- Verifique se a porta 587 não está bloqueada pelo firewall
- Tente usar a porta 465 com SSL (altere `MAIL_SMTP_ENCRYPTION` para `ssl`)

**Emails não estão sendo enviados**
- Verifique os logs do PHP (habilitar `error_log` no PHP)
- Verifique se `MAIL_SMTP_ENABLED=true` no `.env`
- Teste a conexão SMTP manualmente

### Alternativas

Se não quiser usar Gmail SMTP, você pode:
- Usar outro provedor de email (Outlook, Yahoo, etc.)
- Configurar um servidor de email próprio
- Usar serviços como SendGrid, Mailgun, etc. (requer alteração no código)


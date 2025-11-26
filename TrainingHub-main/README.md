# TrainingHub

Plataforma web para conectar professores de educação física com academias, facilitando a contratação de profissionais e a busca por oportunidades de trabalho.

## 🚀 Funcionalidades

### Para Professores
- Cadastro e gerenciamento de perfil completo
- Busca de freelances disponíveis
- Envio de propostas para academias
- Acompanhamento de propostas enviadas
- Dashboard com estatísticas pessoais

### Para Academias
- Cadastro e gerenciamento de perfil
- Publicação de freelances
- Recebimento e gerenciamento de propostas
- Avaliação de professores
- Dashboard com estatísticas da academia

## 📋 Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL

## 🛠️ Instalação

1. Clone o repositório:
```bash
git clone <url-do-repositorio>
cd TrainingHub
```

2. Configure as variáveis de ambiente:
   - Copie o arquivo `.env.example` para `.env`:
   ```bash
   cp .env.example .env
   ```
   - Edite o arquivo `.env` e configure as variáveis necessárias:
     - `DB_HOST`: Host do banco de dados (padrão: localhost)
     - `DB_DATABASE`: Nome do banco de dados (padrão: traininghub)
     - `DB_USERNAME`: Usuário do banco de dados (padrão: root)
     - `DB_PASSWORD`: Senha do banco de dados
     - `APP_URL`: URL da aplicação (padrão: http://localhost)
     - **Email (Gmail SMTP):**
       - `MAIL_SMTP_ENABLED`: Defina como `true` para usar SMTP
       - `MAIL_SMTP_USERNAME`: Seu email do Gmail
       - `MAIL_SMTP_PASSWORD`: **Senha de App do Gmail** (veja `CONFIGURACAO_EMAIL.md`)
       - ⚠️ **Importante:** Use a senha de app do Gmail, não sua senha normal!

3. Configure o banco de dados:
   - Crie um banco de dados MySQL
   - Execute o script `database.sql` para criar as tabelas:
   ```bash
   mysql -u root -p traininghub < database.sql
   ```

4. Configure o servidor web:
   - Configure o DocumentRoot para apontar para a pasta `public`
   - Ou use o servidor PHP embutido: `php -S localhost:8000 -t public`

5. Acesse a aplicação:
   - Abra o navegador em `http://localhost:8000` (ou a URL configurada no `.env`)

## 📁 Estrutura do Projeto

```
TrainingHub/
├── config/              # Arquivos de configuração
│   ├── app.php
│   ├── database.php
│   └── email.php
├── public/              # Ponto de entrada da aplicação
│   ├── assets/          # CSS, JS e imagens
│   └── index.php        # Front controller
├── src/
│   ├── Controller/      # Controladores
│   ├── Database/        # Classes de banco de dados
│   ├── Models/          # Modelos de dados
│   ├── Repositories/    # Repositórios de dados
│   ├── Services/        # Serviços de negócio
│   ├── Validators/      # Validadores
│   └── Views/           # Templates de visualização
└── database.sql         # Script de criação do banco
```

## 🔐 Segurança

- Senhas são armazenadas com hash usando `password_hash()`
- Validação de dados em formulários
- Proteção contra SQL Injection usando PDO prepared statements
- Sessões seguras para autenticação

## 📝 Licença

Este projeto está sob a licença especificada no arquivo LICENSE.

## 👥 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou pull requests.

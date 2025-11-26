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

2. Configure o banco de dados:
   - Crie um banco de dados MySQL
   - Execute o script `database.sql` para criar as tabelas
   - Configure as credenciais em `config/database.php`

3. Configure o servidor web:
   - Configure o DocumentRoot para apontar para a pasta `public`
   - Ou use o servidor PHP embutido: `php -S localhost:8000 -t public`

4. Acesse a aplicação:
   - Abra o navegador em `http://localhost:8000` (ou a URL configurada)

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

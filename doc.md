/public_html/ (Raiz do servidor web e do projeto)
├── app/
│ ├── Controllers/
│ │ ├── UserController.php
│ │ └── BabyController.php
│ ├── Models/
│ │ ├── User.php
│ │ └── Baby.php
│ ├── Views/
│ │ ├── User/
│ │ │ └── register.php
│ │ └── Baby/
│ │ └── show.php
│ └── Core/
│ ├── Router.php
│ ├── Controller.php
│ └── Database.php
├── config/
│ └── database.php
├── vendor/ // Para dependências (se usado)
├── .htaccess // Para reescrever URLs
├── index.php // Ponto de entrada da aplicação
├── css/
└── js/

ALTER TABLE `users`
ADD COLUMN `email_verification_token` VARCHAR(255) NULL DEFAULT NULL AFTER `password`,
ADD COLUMN `email_verification_expires_at` TIMESTAMP NULL DEFAULT NULL AFTER `email_verification_token`,
ADD UNIQUE INDEX `idx_email_verification_token` (`email_verification_token`);

## Status dos Requisitos Funcionais

### RF01: Cadastro de Usuário

- **Status**: Concluído (incluindo confirmação de e-mail).

### RF02: Login de Usuário

- **Status**: Parcialmente Concluído.
- **Implementado**:
  - Autenticação via e-mail e senha.
  - Verificação se o e-mail do usuário foi confirmado antes do login.
  - Logout do usuário.
- **Pendente**:
  - Suporte a recuperação de senha via e-mail.
  - Bloqueio temporário após 5 tentativas de login incorretas.

### RF03: Cadastro de Bebê Reborn

- **Status**: Concluído.
- **Implementado**:
  - Formulário de cadastro de bebê.
  - Lógica de salvamento dos dados do bebê no banco.
  - Geração de `registration_number` (UUID).
  - Upload de imagem do bebê.
  - Geração e atualização do `civil_registration`.
  - View para exibir detalhes do bebê (`Baby/show.php`).
  - View de erro 404 (`Errors/404.php`).
  - Validações completas dos campos do formulário.

### RF04: Geração de Documentos

- **Status**: Parcialmente Concluído.
- **Implementado**:
  - Geração de Certidão de Nascimento em PDF.
  - Inclusão de QR Code com URL de validação na Certidão de Nascimento.
  - Download imediato do PDF da Certidão de Nascimento.
  - Imagem de fundo dinâmica na Certidão de Nascimento (baseada no gênero).
- **Pendente**:
  - Geração de RG em PDF.
  - Layouts finais dos documentos (atualmente simplificados).

### RF05: Validação de Documentos

- **Status**: Parcialmente Concluído.
- **Implementado**:
  - Validação via URL (destino do QR Code) que exibe os dados do bebê (`/baby/{registration_number}`).
- **Pendente**:
  - Interface para inserir número de registro manualmente para validação.
  - Interface para leitura de QR Code via câmera (suporte a dispositivos móveis).

### RF06: API REST para Consulta e Validação

- **Status**: Parcialmente Concluído.
- **Implementado**:
  - Endpoint `GET /api/babies/{civil_registration}` para retornar dados do bebê (público).
  - Endpoint `GET /api/validate/{civil_registration}` para validar a existência do registro (público).
  - Respostas em JSON com códigos de status HTTP apropriados.
- **Pendente**:
  - Autenticação via chave API (estrutura implementada, mas não obrigatória nos endpoints atuais).
  - Documentação da API em formato OpenAPI/Swagger.
  - Mecanismo para usuários gerarem/gerenciarem suas chaves API.

# Documentação de Requisitos para o PWA "R-Born Id" (MVP)

## 1. Visão Geral
O "R-Born Id" é um Progressive Web App (PWA) voltado para o registro de bebês reborn, permitindo que usuários criem e gerenciem documentos oficiais, como RG e certidão de nascimento, no padrão brasileiro. O sistema gerará documentos com QR Codes para validação e oferecerá uma API REST para consulta e validação de registros por outras aplicações.

### 1.1 Objetivo
Desenvolver um MVP funcional que permita:
- Cadastro de bebês reborn com informações detalhadas.
- Geração de documentos (RG e certidão de nascimento) em formato PDF com QR Code para validação.
- Validação de documentos por meio de número de registro ou leitura de QR Code.
- Integração via API REST para consulta de dados e validação por sistemas externos.

### 1.2 Escopo do MVP
- **Funcionalidades principais**:
  - Cadastro de usuários.
  - Cadastro de bebês reborn.
  - Geração de RG e certidão de nascimento em PDF com QR Code.
  - Validação de documentos via número de registro ou QR Code.
  - API REST para consulta e validação de registros.
- **Limitações do MVP**:
  - Interface simples e focada em funcionalidade.
  - Suporte apenas para documentos no padrão brasileiro.
  - Sem funcionalidades avançadas de edição de documentos ou integração com redes sociais.

## 2. Requisitos Funcionais

### RF01: Cadastro de Usuário
- **Descrição**: O sistema deve permitir que novos usuários se cadastrem para acessar as funcionalidades do PWA.
- **Critérios de Aceitação**:
  - Campos obrigatórios: nome, e-mail, CPF, senha.
  - Validação de e-mail único e CPF válido (formato brasileiro).
  - Senha com no mínimo 8 caracteres, incluindo letras, números e caracteres especiais.
  - Confirmação de cadastro via e-mail com link de ativação.
- **Prioridade**: Alta.

### RF02: Login de Usuário
- **Descrição**: O sistema deve permitir login de usuários cadastrados.
- **Critérios de Aceitação**:
  - Autenticação via e-mail e senha.
  - Suporte a recuperação de senha via e-mail.
  - Bloqueio temporário após 5 tentativas de login incorretas.
- **Prioridade**: Alta.

### RF03: Cadastro de Bebê Reborn
- **Descrição**: O usuário poderá registrar um bebê reborn com informações detalhadas.
- **Critérios de Aceitação**:
  - Campos obrigatórios: nome do bebê, data de "nascimento", gênero, peso, altura, nome do artesão, data de criação.
  - Campos opcionais: características especiais (ex.: cor dos olhos, cabelo), número de série (se aplicável).
  - Geração automática de um número de registro único para cada bebê.
  - Validação de campos (ex.: data válida, peso e altura em formatos numéricos).
- **Prioridade**: Alta.

### RF04: Geração de Documentos
- **Descrição**: O sistema deve gerar RG e certidão de nascimento em PDF com base nos dados do bebê.
- **Critérios de Aceitação**:
  - **RG**: Inclui nome, número de registro, data de "nascimento", nome do artesão, e QR Code.
  - **Certidão de Nascimento**: Inclui nome, número de registro, data de "nascimento", local (cidade/estado), nome do artesão, e QR Code.
  - QR Code deve conter URL para validação (ex.: `https://r-born-id.com/validar/{numero_registro}`).
  - Documentos gerados devem seguir layout padrão brasileiro (modelos visuais de RG e certidão).
  - Download imediato do PDF após geração.
- **Prioridade**: Alta.

### RF05: Validação de Documentos
- **Descrição**: O sistema deve permitir validar documentos via leitura de QR Code ou número de registro.
- **Critérios de Aceitação**:
  - Interface para inserir número de registro manualmente.
  - Interface para leitura de QR Code via câmera (suporte a dispositivos móveis).
  - Exibição dos dados do bebê (nome, data de "nascimento", artesão) ao validar.
  - Mensagem de erro para registros inválidos.
- **Prioridade**: Alta.

### RF06: API REST para Consulta e Validação
- **Descrição**: O sistema deve fornecer uma API REST para consulta de dados de bebês e validação de registros.
- **Critérios de Aceitação**:
  - **Endpoint 1**: `GET /api/babies/{numero_registro}` - Retorna dados do bebê (nome, data de "nascimento", artesão, etc.).
  - **Endpoint 2**: `GET /api/validate/{numero_registro}` - Valida a existência do registro e retorna status.
  - Autenticação via chave API (enviada no header `Authorization`).
  - Respostas em JSON com códigos de status HTTP apropriados (200, 404, 401, etc.).
  - Documentação da API em formato OpenAPI/Swagger.
- **Prioridade**: Alta.

## 3. Requisitos Não Funcionais

### RNF01: Desempenho
- O sistema deve responder a requisições em até 2 segundos em condições normais.
- A geração de PDFs deve ser concluída em até 5 segundos.
- A API deve suportar até 100 requisições por segundo.

### RNF02: Segurança
- Dados sensíveis (CPF, senha) devem ser armazenados com criptografia (ex.: bcrypt para senhas).
- Comunicação via HTTPS com certificado SSL/TLS.
- Autenticação JWT para endpoints da API.
- Proteção contra ataques comuns (SQL Injection, XSS, CSRF).

### RNF03: Compatibilidade
- O PWA deve ser compatível com navegadores modernos (Chrome, Firefox, Safari, Edge) em desktops e dispositivos móveis.
- Suporte a resoluções de tela a partir de 320x480px (mínimo para dispositivos móveis).
- Funcionalidade offline para visualização de documentos salvos localmente.

### RNF04: Escalabilidade
- O sistema deve suportar até 10.000 usuários cadastrados no MVP.
- Banco de dados deve ser otimizado para consultas rápidas em tabelas de registros.

### RNF05: Usabilidade
- Interface intuitiva com design responsivo.
- Feedback visual para ações do usuário (ex.: carregamento, sucesso, erro).
- Suporte a idioma português (BR) no MVP.

## 4. Requisitos Técnicos

### 4.1 Tecnologias
- **Backend**: PHP 8.3+ (puro, sem frameworks como Laravel para o MVP).
- **Frontend**: HTML5, CSS3, JavaScript (ES6+), com suporte a PWA (Service Worker, Manifest).
- **Banco de Dados**: MySQL 8.0+ ou PostgreSQL 15+ (recomendado para escalabilidade).
- **Bibliotecas**:
  - **FPDF** ou **TCPDF** para geração de PDFs.
  - **PHP QR Code** para geração de QR Codes.
  - **JWT PHP** para autenticação na API.
- **Hospedagem**: Servidor com suporte a PHP e HTTPS (ex.: Apache, Nginx).

### 4.2 Estrutura do Banco de Dados
- **Tabela: users**
  - id (PK, auto-increment)
  - name (varchar)
  - email (varchar, unique)
  - cpf (varchar, unique)
  - password (varchar, hashed)
  - created_at (timestamp)
- **Tabela: babies**
  - id (PK, auto-increment)
  - user_id (FK, references users.id)
  - registration_number (varchar, unique)
  - name (varchar)
  - birth_date (date)
  - gender (enum: M/F/Other)
  - weight (decimal)
  - height (decimal)
  - artisan_name (varchar)
  - creation_date (date)
  - characteristics (text, nullable)
  - serial_number (varchar, nullable)
  - created_at (timestamp)
- **Tabela: api_keys**
  - id (PK, auto-increment)
  - user_id (FK, references users.id)
  - api_key (varchar, unique)
  - created_at (timestamp)

### 4.3 Arquitetura da Aplicação
- **Modelo**: MVC (adaptado para PHP puro).
  - **Model**: Lógica de acesso ao banco de dados (CRUD).
  - **View**: Templates HTML com CSS e JS para o frontend.
  - **Controller**: Scripts PHP para processar requisições e coordenar a lógica.
- **API REST**:
  - Estrutura de endpoints em `/api/` com roteamento simples via PHP.
  - Respostas padronizadas em JSON.
  - Autenticação via JWT ou chave API.
- **PWA**:
  - Service Worker para cache de assets e suporte offline.
  - Manifest.json com ícone, nome e configurações do PWA.
  - Interface responsiva com CSS Grid ou Flexbox.

### 4.4 Fluxo de Geração de Documentos
1. Usuário cadastra bebê reborn.
2. Sistema gera número de registro único (ex.: UUID ou hash baseado em dados).
3. Dados são salvos no banco.
4. Sistema usa biblioteca FPDF/TCPDF para criar PDF com layout de RG ou certidão.
5. QR Code é gerado com URL de validação e incorporado ao PDF.
6. PDF é oferecido para download.

### 4.5 Fluxo de Validação
1. Usuário insere número de registro ou escaneia QR Code.
2. Sistema consulta banco de dados pelo número de registro.
3. Se válido, exibe dados do bebê; caso contrário, exibe erro.

## 5. Requisitos de Implantação
- Servidor com PHP 8.3+, MySQL/PostgreSQL, e suporte a HTTPS.
- Configuração de CORS para a API (permitir chamadas de outros domínios, se necessário).
- Backup diário do banco de dados.
- Monitoramento de erros via logs (ex.: PHP error log).

## 6. Requisitos de Testes
- **Testes Unitários**: Validação de funções PHP (ex.: geração de QR Code, hash de senhas).
- **Testes de Integração**: Fluxos completos (cadastro, geração de documento, validação).
- **Testes de Usabilidade**: Interface testada em dispositivos móveis e desktops.
- **Testes de Segurança**: Verificação contra SQL Injection, XSS, e CSRF.

## 7. Cronograma Estimado (MVP)
- **Planejamento e Design**: 1 semana.
- **Desenvolvimento Backend (API + Lógica)**: 3 semanas.
- **Desenvolvimento Frontend (PWA)**: 2 semanas.
- **Geração de Documentos e QR Code**: 1 semana.
- **Testes e Correções**: 1 semana.
- **Total**: ~8 semanas.

## 8. Premissas e Restrições
- **Premissas**:
  - Usuários têm acesso a dispositivos com câmera para leitura de QR Code.
  - A aplicação será hospedada em servidor com suporte a PHP e MySQL/PostgreSQL.
- **Restrições**:
  - Orçamento limitado para o MVP (evitar frameworks pesados ou serviços pagos).
  - Prazo de 8 semanas para entrega do MVP.
  - Suporte apenas ao idioma português (BR).

## 9. Próximos Passos
- Definir layout visual dos documentos (RG e certidão).
- Escolher biblioteca de PDF (FPDF ou TCPDF).
- Configurar ambiente de desenvolvimento (servidor local com PHP/MySQL).
- Iniciar desenvolvimento do backend (API e lógica de cadastro).
- Planejar testes de usabilidade com usuários reais.
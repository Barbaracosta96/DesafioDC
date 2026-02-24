# CINDEC — Plataforma de Gestão Operacional SaaS

> Aplicação SaaS full-stack desenvolvida com **Laravel 12**, **Vue.js 3 + Inertia.js** e **Tailwind CSS 4**, containerizada com **Docker**. Voltada ao controle de ativos, estoque, ordens de venda e gestão de acesso (ACL) para equipes operacionais.

---

## Índice

- [Stack Tecnológica](#stack-tecnológica)
- [Arquitetura](#arquitetura)
- [Módulos da Aplicação](#módulos-da-aplicação)
- [Gestão de Acesso (ACL)](#gestão-de-acesso-acl)
- [Diagrama do Banco de Dados](#diagrama-do-banco-de-dados)
- [Modelo de Negócio](#modelo-de-negócio)
- [Estrutura de Pastas](#estrutura-de-pastas)
- [Configuração e Execução](#configuração-e-execução)
- [Credenciais Padrão](#credenciais-padrão)
- [Testes Automatizados](#testes-automatizados)
- [Variáveis de Ambiente](#variáveis-de-ambiente)

---

## Stack Tecnológica

| Camada          | Tecnologia                                   |
|-----------------|----------------------------------------------|
| Backend         | PHP 8.4 + Laravel 12                         |
| Frontend        | Vue.js 3 + Inertia.js 2                      |
| Estilização     | Tailwind CSS 4                               |
| Banco de Dados  | MySQL 8.0                                    |
| Autenticação    | Laravel Session Auth + Spatie Permission     |
| Infraestrutura  | Docker (PHP-FPM, Nginx, MySQL, Node/Vite)    |
| Testes          | PHPUnit 11                                   |
| Build Frontend  | Vite 6 + @tailwindcss/vite                   |

---

## Arquitetura

A aplicação segue princípios de **Clean Code** e **SOLID**, organizada em camadas:

```
HTTP Request
    │
    ▼
Form Request (validação + sanitização)
    │
    ▼
Controller (orquestração, sem lógica de negócio)
    │
    ▼
Service Layer (VendaService, EstoqueService — regras de negócio)
    │
    ▼
Eloquent Model (persistência + relacionamentos)
    │
    ▼
Inertia Response → Vue.js SPA (sem API JSON exposta)
```

**Decisões arquiteturais:**
- **Inertia.js** elimina a necessidade de uma API REST separada, mantendo o ciclo request/response do Laravel enquanto entrega componentes Vue como SPA.
- **Service Layer** isola regras críticas (cálculo de totais, movimentação de estoque, geração de número de pedido) dos controllers.
- **Form Requests** centralizam validação e autorização, mantendo os controllers enxutos.
- **Spatie Laravel Permission** gerencia ACL com roles/permissions granulares via middleware.
- **SoftDeletes** em todas as entidades críticas para preservação histórica.

---

## Módulos da Aplicação

### Dashboard
- KPIs em tempo real: receita mensal, total de vendas, produtos em estoque, clientes ativos
- Gráfico de receita dos últimos 6 meses
- Top 5 produtos mais vendidos
- Últimas 8 vendas realizadas
- Alertas de estoque abaixo do mínimo

### Estoque (Produtos)
- CRUD completo de produtos com código SKU único
- Vínculo por categorias
- Alertas automáticos de estoque mínimo
- Histórico de movimentações (entrada, saída, ajuste) com log de quem realizou
- Filtros por nome, SKU, categoria e status

### Vendas
- CRUD com geração automática de número de pedido (`PED-000001`)
- Múltiplos itens por venda com cálculo automático de subtotais e descontos
- Status: `pendente` → `processando` → `concluído` → `cancelado`
- Formas de pagamento: dinheiro, cartão de crédito/débito, PIX
- Movimentação automática de estoque na conclusão da venda
- Exportação em CSV
- Filtros por status, data e cliente

### Clientes
- CRUD com suporte a Pessoa Física (CPF) e Pessoa Jurídica (CNPJ)
- Endereço completo (logradouro, bairro, cidade, estado, CEP)
- Histórico de compras por cliente
- Resumo de total de compras e valor total gasto

### Categorias
- Gerenciamento inline via modal (sem navegação de página)
- Contagem de produtos vinculados
- Proteção contra exclusão quando há produtos associados

### Usuários (Admin only)
- CRUD completo de usuários do sistema
- Atribuição de roles (admin, editor, usuário)
- Ativação/desativação de contas
- Histórico de vendas e movimentações por usuário

---

## Gestão de Acesso (ACL)

Baseada em **Spatie Laravel Permission** com 3 roles e 20 permissões granulares:

| Permissão               | Admin | Editor | Usuário |
|-------------------------|:-----:|:------:|:-------:|
| `ver-dashboard`         |  ✅   |   ✅   |   ✅    |
| `ver-estoque`           |  ✅   |   ✅   |   ✅    |
| `criar-estoque`         |  ✅   |   ✅   |   ❌    |
| `editar-estoque`        |  ✅   |   ✅   |   ❌    |
| `excluir-estoque`       |  ✅   |   ❌   |   ❌    |
| `ver-vendas`            |  ✅   |   ✅   |   ✅    |
| `criar-vendas`          |  ✅   |   ✅   |   ❌    |
| `editar-vendas`         |  ✅   |   ✅   |   ❌    |
| `excluir-vendas`        |  ✅   |   ❌   |   ❌    |
| `ver-clientes`          |  ✅   |   ✅   |   ✅    |
| `criar-clientes`        |  ✅   |   ✅   |   ❌    |
| `editar-clientes`       |  ✅   |   ✅   |   ❌    |
| `excluir-clientes`      |  ✅   |   ❌   |   ❌    |
| `ver-categorias`        |  ✅   |   ✅   |   ✅    |
| `criar-categorias`      |  ✅   |   ✅   |   ❌    |
| `editar-categorias`     |  ✅   |   ✅   |   ❌    |
| `excluir-categorias`    |  ✅   |   ❌   |   ❌    |
| `ver-usuarios`          |  ✅   |   ❌   |   ❌    |
| `criar-usuarios`        |  ✅   |   ❌   |   ❌    |
| `editar-usuarios`       |  ✅   |   ❌   |   ❌    |
| `excluir-usuarios`      |  ✅   |   ❌   |   ❌    |

---

## Diagrama do Banco de Dados

```
┌─────────────────────┐
│        users        │
├─────────────────────┤
│ id (PK)             │
│ name                │
│ email (unique)      │
│ password            │
│ ativo               │
│ email_verified_at   │
│ remember_token      │
│ created_at          │
│ updated_at          │
└─────────┬───────────┘
          │ 1:N                           ┌──────────────────────────────┐
          │                               │         roles / permissions   │
          │ (Spatie HasRoles)             │  (model_has_roles, etc.)      │
          └──────────────────────────────►│                              │
                                          └──────────────────────────────┘

┌─────────────────────┐        ┌────────────────────────────┐
│      categorias     │        │          produtos           │
├─────────────────────┤        ├────────────────────────────┤
│ id (PK)             │◄───────│ id (PK)                    │
│ nome                │  N:1   │ categoria_id (FK, nullable) │
│ descricao           │        │ nome                       │
│ ativo               │        │ codigo_sku (unique)        │
│ created_at          │        │ descricao                  │
│ updated_at          │        │ preco_custo                │
│ deleted_at          │        │ preco_venda                │
└─────────────────────┘        │ quantidade_estoque         │
                               │ estoque_minimo             │
                               │ unidade                    │
                               │ imagem                     │
                               │ ativo                      │
                               │ created_at                 │
                               │ updated_at                 │
                               │ deleted_at                 │
                               └───────┬────────────────────┘
                                       │ 1:N
          ┌────────────────────────────┘
          │
          ▼
┌─────────────────────────────┐        ┌──────────────────────────┐
│    movimentacoes_estoque     │        │         clientes         │
├─────────────────────────────┤        ├──────────────────────────┤
│ id (PK)                     │        │ id (PK)                  │
│ produto_id (FK → produtos)  │        │ nome                     │
│ user_id    (FK → users)     │        │ email (unique)           │
│ venda_id   (FK → vendas)    │        │ telefone                 │
│ tipo (entrada/saida/ajuste) │        │ cpf_cnpj (unique)        │
│ quantidade                  │        │ tipo (pf/pj)             │
│ quantidade_anterior         │        │ cep                      │
│ quantidade_posterior        │        │ logradouro               │
│ motivo                      │        │ numero                   │
│ created_at                  │        │ bairro                   │
│ updated_at                  │        │ cidade                   │
└─────────────────────────────┘        │ estado                   │
                                       │ ativo                    │
                                       │ created_at               │
                                       │ updated_at               │
                                       │ deleted_at               │
                                       └──────────┬───────────────┘
                                                  │ 1:N
                                                  ▼
┌────────────────────────────────────────────────────────────┐
│                           vendas                           │
├────────────────────────────────────────────────────────────┤
│ id (PK)                                                    │
│ numero_pedido (unique)  ex: PED-000001                     │
│ cliente_id (FK → clientes, nullable)                       │
│ user_id    (FK → users)                                    │
│ status    (pendente/processando/concluido/cancelado)       │
│ forma_pagamento  (dinheiro/cartao_credito/debito/pix)      │
│ subtotal                                                   │
│ desconto                                                   │
│ total                                                      │
│ observacoes                                                │
│ data_venda                                                 │
│ created_at                                                 │
│ updated_at                                                 │
│ deleted_at                                                 │
└───────────────────────────┬────────────────────────────────┘
                            │ 1:N
                            ▼
              ┌─────────────────────────────┐
              │         itens_venda         │
              ├─────────────────────────────┤
              │ id (PK)                     │
              │ venda_id   (FK → vendas)    │
              │ produto_id (FK → produtos)  │
              │ quantidade                  │
              │ preco_unitario              │
              │ desconto                    │
              │ subtotal                    │
              │ created_at                  │
              │ updated_at                  │
              └─────────────────────────────┘
```

### Relacionamentos Eloquent resumidos

| Model                | Relacionamentos                                                  |
|----------------------|------------------------------------------------------------------|
| `User`               | `hasMany(Venda)`, `hasMany(MovimentacaoEstoque)`, `HasRoles`     |
| `Categoria`          | `hasMany(Produto)`                                               |
| `Produto`            | `belongsTo(Categoria)`, `hasMany(ItemVenda)`, `hasMany(MovimentacaoEstoque)` |
| `Cliente`            | `hasMany(Venda)`                                                 |
| `Venda`              | `belongsTo(Cliente)`, `belongsTo(User)`, `hasMany(ItemVenda)`, `hasMany(MovimentacaoEstoque)` |
| `ItemVenda`          | `belongsTo(Venda)`, `belongsTo(Produto)`                        |
| `MovimentacaoEstoque`| `belongsTo(Produto)`, `belongsTo(User)`, `belongsTo(Venda)`     |

---

## Modelo de Negócio

```
CINDEC SaaS — Fluxo Operacional
════════════════════════════════

  [Cadastro / Login]
         │
         ▼
  [Dashboard] ◄──── KPIs em tempo real
         │
  ┌──────┴──────────────────────────────┐
  │                                     │
  ▼                                     ▼
[Estoque]                          [Vendas]
  │                                     │
  │ Cadastra produto                    │ Seleciona cliente
  │ Define estoque mínimo               │ Adiciona itens
  │ Movimentação rastreada              │ Aplica desconto
  │                                     │ Define pagamento
  │                                     │
  │         ┌───────────────────────────┘
  │         │ Ao CONCLUIR venda:
  │         │ → Baixa automática no estoque
  │         │ → Registra MovimentacaoEstoque
  │         │ → Atualiza KPIs do Dashboard
  │         │
  ▼         ▼
[Categoria] ─► organiza produtos
[Clientes]  ─► histórico de compras
[Usuários]  ─► controle de acesso (admin only)
```

---

## Estrutura de Pastas

```
DesafioDC/
├── docker/
│   ├── mysql/init/          # Scripts SQL de inicialização
│   ├── nginx/               # Configuração Nginx + Dockerfile
│   ├── node/                # Dockerfile Node/Vite
│   └── php/                 # Dockerfile PHP-FPM + entrypoint + configs
├── src/                     # Aplicação Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Auth/              # Login, Cadastro, EsqueciSenha, RedefinirSenha
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── EstoqueController.php
│   │   │   │   ├── VendasController.php
│   │   │   │   ├── ClientesController.php
│   │   │   │   ├── CategoriasController.php
│   │   │   │   └── UsuariosController.php
│   │   │   └── Requests/
│   │   │       ├── Auth/              # LoginRequest, CadastroRequest, etc.
│   │   │       ├── StoreProdutoRequest.php
│   │   │       ├── UpdateProdutoRequest.php
│   │   │       ├── StoreVendaRequest.php
│   │   │       ├── UpdateVendaRequest.php
│   │   │       ├── StoreClienteRequest.php
│   │   │       ├── UpdateClienteRequest.php
│   │   │       ├── StoreCategoriaRequest.php
│   │   │       ├── UpdateCategoriaRequest.php
│   │   │       ├── StoreUsuarioRequest.php
│   │   │       └── UpdateUsuarioRequest.php
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── Categoria.php
│   │   │   ├── Produto.php
│   │   │   ├── Cliente.php
│   │   │   ├── Venda.php
│   │   │   ├── ItemVenda.php
│   │   │   └── MovimentacaoEstoque.php
│   │   └── Services/
│   │       ├── EstoqueService.php     # Criação, atualização, movimentações
│   │       └── VendaService.php       # Criação, atualização, cancelamento
│   ├── database/
│   │   ├── migrations/                # 10 migrations estruturadas
│   │   ├── seeders/                   # Roles, Usuários, Produtos, Vendas
│   │   └── factories/                 # UserFactory, ClienteFactory, ProdutoFactory
│   ├── resources/
│   │   └── js/
│   │       ├── app.js                 # Entry point Inertia + Ziggy
│   │       ├── Layouts/AppLayout.vue  # Layout principal com sidebar
│   │       ├── Components/            # Badge, Botao, InputField, Modal, etc.
│   │       └── Pages/
│   │           ├── Auth/              # Login, Cadastro, EsqueciSenha, RedefinirSenha
│   │           ├── Dashboard/Index.vue
│   │           ├── Estoque/           # Index, Formulario, Visualizar
│   │           ├── Vendas/            # Index, Formulario, Visualizar
│   │           ├── Clientes/          # Index, Formulario, Visualizar
│   │           ├── Categorias/        # Index (com modal inline)
│   │           └── Usuarios/          # Index, Formulario, Visualizar
│   ├── routes/web.php
│   └── tests/
│       ├── Feature/                   # AuthTest, AclTest, VendaTest, etc.
│       └── Unit/                      # EstoqueServiceTest, VendaServiceTest
└── docker-compose.yml
```

---

## Configuração e Execução

### Pré-requisitos

- Docker Desktop (Windows/Mac) ou Docker Engine + Docker Compose (Linux)
- Git

### 1. Clonar o repositório

```bash
git clone https://github.com/MatheusEstrela-dev/DesafioDC.git
cd DesafioDC
```

### 2. Configurar variáveis de ambiente

```bash
cp src/.env.example src/.env
```

Edite `src/.env` e configure os dados do banco:

```dotenv
APP_NAME="CINDEC"
APP_URL=http://localhost:8081

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### 3. Subir os containers

```bash
docker-compose up -d --build
```

Os serviços expostos são:

| Serviço     | URL / Porta         |
|-------------|---------------------|
| Aplicação   | http://localhost:8081 |
| Vite (Dev)  | http://localhost:5173 |
| MySQL       | localhost:3306      |

### 4. Instalar dependências e configurar o Laravel

```bash
# Entrar no container da aplicação
docker exec -it laravel-app bash

# Dentro do container:
composer install
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
exit
```

> **Alternativa com just** (se você tiver o `just` instalado):
> ```bash
> just install    # primeira instalação completa
> just up         # iniciar containers
> just seed       # executar seeders
> ```

### 5. Acessar a aplicação

Abra http://localhost:8081 e faça login com as [credenciais padrão](#credenciais-padrão).

---

## Credenciais Padrão

Criadas automaticamente pelo seeder `UsuariosSeeder`:

| Role    | E-mail               | Senha      | Acesso                        |
|---------|----------------------|------------|-------------------------------|
| Admin   | admin@base.com       | `password` | Acesso total ao sistema       |
| Editor  | editor@base.com      | `password` | Estoque, Vendas e Clientes    |
| Usuário | usuario@base.com     | `password` | Somente visualização          |

---

## Testes Automatizados

A suíte de testes cobre os principais fluxos da aplicação:

```bash
# Dentro do container:
php artisan test

# Com cobertura de código:
php artisan test --coverage
```

### Testes de Feature (`tests/Feature/`)

| Arquivo           | Cenários                                                        |
|-------------------|-----------------------------------------------------------------|
| `AuthTest`        | Login, logout, cadastro, restrição de acesso não autenticado    |
| `AclTest`         | Bloqueio por role, acesso admin vs editor vs usuário            |
| `VendaTest`       | CRUD de vendas, validações, baixa de estoque                    |
| `EstoqueTest`     | CRUD de produtos, filtros, movimentações                        |
| `ClienteTest`     | CRUD de clientes, validações, unicidade CPF/CNPJ                |

### Testes de Unidade (`tests/Unit/`)

| Arquivo                | Cenários                                                       |
|------------------------|----------------------------------------------------------------|
| `EstoqueServiceTest`   | Criação com estoque inicial, ajuste, movimentação avulsa       |
| `VendaServiceTest`     | Cálculo de totais, geração de número, cancelamento             |

---

## Variáveis de Ambiente

| Variável          | Valor padrão no Docker | Descrição                        |
|-------------------|------------------------|----------------------------------|
| `DB_CONNECTION`   | `mysql`                | Driver de banco de dados         |
| `DB_HOST`         | `db`                   | Host do MySQL (service no Docker)|
| `DB_PORT`         | `3306`                 | Porta do MySQL                   |
| `DB_DATABASE`     | `laravel`              | Nome do banco                    |
| `DB_USERNAME`     | `laravel`              | Usuário do banco                 |
| `DB_PASSWORD`     | `secret`               | Senha do banco                   |
| `APP_URL`         | `http://localhost:8081`| URL base da aplicação            |
| `SESSION_DRIVER`  | `database`             | Driver de sessão                 |
| `CACHE_STORE`     | `database`             | Driver de cache                  |
| `QUEUE_CONNECTION`| `database`             | Driver de filas                  |
| `MAIL_MAILER`     | `log`                  | Driver de e-mail (logs em dev)   |

---

## Segurança

- **Form Requests** em todos os endpoints: validação + autorização centralizadas
- **ACL Middleware** (`role:admin`) protege rotas administrativas
- **Permission checks** (`abort_unless($user->can(...))`) dentro dos controllers para operações críticas
- **SoftDeletes** em todas as entidades principais (dados nunca são deletados fisicamente)
- **CSRF** protegido nativamente pelo Laravel em todos os formulários Inertia
- **SQL Injection** prevenido pelo Eloquent ORM e Query Builder parametrizados
- **XSS** protegido pelo Vue.js (escaping automático de templates)
- Senhas com `bcrypt` (rounds configurável via `BCRYPT_ROUNDS`)

---

## Desenvolvido por Barbara Costa

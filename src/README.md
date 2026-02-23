# CINDEC/MG — Sistema de Gestão Operacional em Defesa Civil

Plataforma SaaS de gestão operacional do **Centro de Inteligência em Defesa Civil de Minas Gerais**, desenvolvida com **Laravel 11**, **Vue 3**, **Inertia.js** e **Tailwind CSS v4**, rodando em ambiente **Docker**.

O sistema gerencia ativos tecnológicos, ordens de fornecimento, entidades parceiras e operações da Defesa Civil mineira.

---

## Sumario

- [Tecnologias](#tecnologias)
- [Arquitetura](#arquitetura)
- [Pré-requisitos](#pre-requisitos)
- [Configuracao e Execucao](#configuracao-e-execucao)
- [Credenciais de Acesso](#credenciais-de-acesso)
- [Comandos Uteis (Justfile)](#comandos-uteis-justfile)
- [Executando os Testes](#executando-os-testes)
- [Modulos](#modulos)
- [Estrutura do Projeto](#estrutura-do-projeto)

---

## Tecnologias

| Camada       | Tecnologia                          |
|--------------|-------------------------------------|
| Backend      | PHP 8.4, Laravel 11                 |
| Frontend     | Vue 3, Inertia.js v2                |
| Estilizacao  | Tailwind CSS v4                     |
| Build        | Vite 7 com `@tailwindcss/vite`      |
| Banco        | MySQL 8.0                           |
| Auth/ACL     | Spatie Laravel Permission           |
| Testes       | PHPUnit 11                          |
| Ambiente     | Docker + Docker Compose             |
| Orquestrador | [Just](https://github.com/casey/just) |

---

## Arquitetura

O projeto segue os principios **SOLID** e **Clean Code**, com separacao clara de responsabilidades:

```
app/
+-- Http/
|   +-- Controllers/        # Apenas orquestracao HTTP
|   +-- Requests/           # Form Requests - validacao e sanitizacao de entrada
|   +-- Resources/          # API Resources
+-- Models/                 # Eloquent Models com relacionamentos e accessors
+-- Services/               # Camada de negocio isolada e testavel
    +-- VendaService.php    # Criacao de vendas, cancelamento com estorno de estoque
    +-- EstoqueService.php  # Criacao e atualizacao de produtos com movimentacoes
```

### Fluxo de uma requisicao

```
Request -> FormRequest (valida/sanitiza) -> Controller (injeta Service) -> Service (logica de negocio) -> Model (persiste)
```

### Form Requests criados

| Arquivo | Responsabilidade |
|---------|-----------------|
| `StoreVendaRequest` | Valida nova venda e itens |
| `UpdateVendaRequest` | Valida atualizacao de status |
| `StoreProdutoRequest` | Valida criacao de produto |
| `UpdateProdutoRequest` | Idem, com unicidade ignorando proprio ID |
| `StoreClienteRequest` | Valida dados de cliente (CPF/CNPJ, e-mail unicos) |
| `UpdateClienteRequest` | Idem com regras de exclusao do proprio registro |
| `StoreCategoriaRequest` | Valida nome unico de categoria |
| `UpdateCategoriaRequest` | Idem com exclusao do proprio registro |

---

## Pre-requisitos

- [Docker](https://docs.docker.com/get-docker/) e Docker Compose
- [Just](https://github.com/casey/just#installation) (orquestrador de tarefas)

---

## Configuracao e Execucao

### 1. Clonar o repositorio

```bash
git clone <url-do-repositorio>
cd DesafioDC
```

### 2. Configurar variaveis de ambiente

```bash
cp src/.env.example src/.env
```

Verifique as variaveis de banco no `src/.env`:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### 3. Subir os containers

```bash
just up
```

Isso sobe os servicos: `app` (PHP-FPM), `webserver` (Nginx), `db` (MySQL) e `node` (Vite).

### 4. Instalar dependencias PHP

```bash
just composer install
```

### 5. Gerar chave da aplicacao

```bash
just artisan key:generate
```

### 6. Executar migrations e seeders

```bash
just fresh
```

Ou apenas migrations sem recriar o banco:

```bash
just migrate
```

### 7. Instalar dependencias JS e compilar assets

```bash
just npm install
just npm run build
```

Para desenvolvimento com hot-reload:

```bash
just npm run dev
```

### 8. Acessar a aplicacao

| Servico   | URL                       |
|-----------|---------------------------|
| Aplicacao | http://localhost:8081      |
| Vite HMR  | http://localhost:5173      |
| MySQL     | localhost:3306             |

---

## Credenciais de Acesso

Apos executar `just fresh` (migrations + seed):

| Perfil        | E-mail                          | Senha      |
|---------------|---------------------------------|------------|
| Administrador | admin@cindec.mg.gov.br          | `password` |
| Editor        | editor@cindec.mg.gov.br         | `password` |
| Operador      | operador@cindec.mg.gov.br       | `password` |

### Permissoes por perfil

| Modulo     | Admin | Editor | Usuario |
|------------|-------|--------|---------|
| Dashboard  | si    | si     | si      |
| Estoque    | CRUD  | CRU    | R       |
| Vendas     | CRUD  | CRU    | R       |
| Clientes   | CRUD  | CRU    | R       |
| Categorias | CRUD  | CR     | R       |
| Usuarios   | CRUD  | nao    | nao     |

---

## Comandos Uteis (Justfile)

```bash
just up              # Sobe todos os containers
just down            # Para todos os containers
just restart         # Reinicia os containers
just logs            # Exibe logs de todos os servicos
just logs app        # Logs de um servico especifico

just migrate         # Executa as migrations
just fresh           # Recria o banco com seed completo
just seed            # Executa apenas os seeders
just rollback        # Desfaz a ultima migration

just artisan <cmd>   # Executa qualquer comando artisan
just composer <cmd>  # Executa comandos do Composer
just npm <cmd>       # Executa comandos do npm

just test            # Executa a suite de testes PHPUnit
just test-api        # Executa testes de API em shell

just clear           # Limpa todos os caches Laravel
just pint            # Formata o codigo com Laravel Pint
```

---

## Executando os Testes

### Testes PHPUnit (Feature + Unit)

```bash
just test
```

Filtrando por classe:

```bash
just artisan test --filter AuthTest
just artisan test --filter EstoqueTest
just artisan test --filter VendaTest
just artisan test --filter ClienteTest
```

### Cobertura de testes implementada

| Arquivo de Teste         | O que cobre |
|--------------------------|-------------|
| `Feature/AuthTest.php`   | Login, logout, redirecionamento sem autenticacao |
| `Feature/EstoqueTest.php`| Criacao com movimentacao de estoque, atualizacao com ajuste, exclusao |
| `Feature/VendaTest.php`  | Criar venda decrementa estoque, cancelamento devolve estoque (estorno) |
| `Feature/ClienteTest.php`| CRUD completo, unicidade de email/cpf, mass assignment protection |

---

## Modulos

### Dashboard
- KPIs: operações do dia, valores operacionais, total de entidades, ativos abaixo do mínimo operacional
- Gráficos: Volume Operacional, Valores Totais, Desempenho Operacional, Volume vs Eficiência
- Tabelas: Ordens Recentes, Ativos em Destaque

### Ativos Tecnológicos (Estoque)
- CRUD de equipamentos e ativos com código de patrimônio/SKU, custo de aquisição, valor de referência, unidade e quantidade mínima operacional
- Histórico completo de movimentações (entrada, saída, ajuste, estorno)
- Alertas de inventário abaixo do mínimo operacional

### Ordens de Fornecimento (Vendas)
- Registro de ordens com múltiplos ativos
- Decremento automático de inventário ao criar
- Estorno automático ao cancelar
- Status: `pendente`, `processando`, `concluido`, `cancelado`
- Formas de pagamento: transferência, empenho, contrato, outros

### Entidades (Clientes)
- Cadastro de órgãos públicos, municípios, coordenadorias e parceiros institucionais
- Campos de endereço completo
- Histórico de ordens por entidade

### Categorias
- Gerenciamento de categorias de ativos tecnológicos
- Protegido contra exclusão de categorias com ativos vinculados

### Usuários *(somente admin)*
- CRUD de operadores com atribuição de roles institucionais
- Roles: `admin`, `editor`, `usuario`

---

## Estrutura do Projeto

```
DesafioDC/
+-- docker/                  # Dockerfiles e configuracoes (PHP, Nginx, Node, MySQL)
+-- src/                     # Codigo-fonte Laravel
|   +-- app/
|   |   +-- Http/
|   |   |   +-- Controllers/ # VendasController, EstoqueController, etc.
|   |   |   +-- Requests/    # 8 Form Requests para validacao e sanitizacao
|   |   +-- Models/          # Eloquent Models
|   |   +-- Services/        # VendaService, EstoqueService
|   +-- database/
|   |   +-- factories/       # ProdutoFactory, ClienteFactory, UserFactory
|   |   +-- migrations/      # Schema do banco (6 migracoes customizadas)
|   |   +-- seeders/         # Dados iniciais (usuarios, produtos, vendas)
|   +-- resources/js/        # Vue 3 Pages, Components, Layouts
|   +-- routes/web.php       # Rotas da aplicacao
|   +-- tests/Feature/       # Testes de feature (PHPUnit)
+-- tests/                   # Testes de integracao em shell
+-- docker-compose.yml
+-- justfile                 # Comandos de orquestracao
```

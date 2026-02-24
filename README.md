# DabangSaaS — Desafio Técnico Full Stack

Sistema de gestão de vendas com painel administrativo, controle de estoque, CRM básico e ACL por papéis.

## Tecnologias

| Camada | Stack |
|---|---|
| Backend | PHP 8.3 · Laravel 12 · Inertia.js (server) |
| Frontend | Vue 3 · Inertia.js (client) · Tailwind CSS v4 |
| Banco de dados | MySQL 8.0 |
| Auth / ACL | Laravel Auth + Spatie Laravel Permission |
| Gráficos | ApexCharts |
| Infra | Docker · Nginx · Vite (HMR) |

## Início Rápido

### Pré-requisitos
- Docker e Docker Compose instalados

### Subindo o ambiente

```bash
docker compose up -d --build
```

O primeiro boot executa automaticamente:
1. `composer install`
2. `php artisan key:generate`
3. `php artisan migrate --force`
4. `php artisan db:seed --force` (apenas na primeira vez)
5. `php artisan storage:link`

A aplicação estará disponível em **http://localhost:8081**

### Assets do frontend

Em desenvolvimento (com HMR):
```bash
docker compose exec node npm run dev
```

Para build de produção:
```bash
docker compose exec node npm run build
```

---

## Contas de Demo

| E-mail | Senha | Papel |
|---|---|---|
| admin@dabang.app | password | Admin |
| editor@dabang.app | password | Editor |
| user@dabang.app | password | User |

---

## Módulos

### Dashboard
- Cards de resumo: total de vendas, receita, produtos ativos, clientes cadastrados
- Gráfico de receita mensal (barras)
- Gráfico de desempenho (linha)
- Top 5 produtos mais vendidos
- Tabela de vendas recentes
- Alerta de estoque crítico

### Estoque (Produtos)
- CRUD completo com upload de imagem
- Calculadora de margem em tempo real
- Filtros por categoria, status e busca textual
- Alertas de estoque mínimo

### Vendas
- Registro de vendas com múltiplos itens
- Desconto por venda
- Controle de status: Pendente / Concluída / Cancelada
- Reversão automática de estoque ao cancelar

### Clientes
- Cadastro completo (endereço, documento, contato)
- Histórico de compras e métricas (ticket médio, total gasto)

### Usuários _(admin only)_
- CRUD de usuários com atribuição de papel (Admin / Editor / User)

---

## ACL — Papéis e Permissões

| Permissão | Admin | Editor | User |
|---|:---:|:---:|:---:|
| view dashboard | ✓ | ✓ | ✓ |
| view/create/edit/delete products | ✓ | ✓ | — |
| view/create sales | ✓ | ✓ | ✓ |
| manage sales (cancel/complete) | ✓ | ✓ | — |
| view/create/edit/delete customers | ✓ | ✓ | — |
| manage users | ✓ | — | — |

---

## Estrutura de Banco de Dados

```
users
  ├── roles (Spatie)
  └── sales → sale_items → products
                             └── categories
customers
  └── sales
```

---

## Arquitetura

```
app/
  Http/
    Controllers/       # thin controllers — delegam para Services
    Middleware/        # HandleInertiaRequests (props globais)
    Requests/          # Form Requests (validação)
  Models/              # Eloquent + relacionamentos
  Services/            # lógica de negócio (SaleService, ProductService)
resources/
  js/
    Pages/             # Vue pages (SSR-ready via Inertia)
      Auth/
      Dashboard/
      Products/
      Sales/
      Users/
      Customers/
      Layouts/
```

---

## Comandos Úteis

```bash
# Acesso ao container PHP
docker compose exec php sh

# Limpar cache
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Re-seed
php artisan db:seed --class=DemoDataSeeder

# Logs em tempo real
docker compose logs -f php
```

---

Desenvolvido por Matheus Estrela

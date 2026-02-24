# CINDEC — Documentação Técnica Laravel

> Esta documentação complementa o [README principal](../README.md) com detalhes técnicos específicos do desenvolvimento Laravel.

---

## Rotas

### Públicas (middleware `guest`)

| Método | URI                          | Controller            | Nome              |
|--------|------------------------------|-----------------------|-------------------|
| GET    | `/entrar`                    | LoginController       | `login`           |
| POST   | `/entrar`                    | LoginController       | `login.store`     |
| GET    | `/cadastro`                  | CadastroController    | `cadastro`        |
| POST   | `/cadastro`                  | CadastroController    | `cadastro.store`  |
| GET    | `/esqueci-a-senha`           | EsqueciSenhaController| `password.request`|
| POST   | `/esqueci-a-senha`           | EsqueciSenhaController| `password.email`  |
| GET    | `/redefinir-senha/{token}`   | RedefinirSenhaController| `password.reset`|
| POST   | `/redefinir-senha`           | RedefinirSenhaController| `password.update`|

### Autenticadas (middleware `auth`)

| Método       | URI                       | Controller             | Nome                    |
|--------------|---------------------------|------------------------|-------------------------|
| GET          | `/`                       | DashboardController    | `dashboard`             |
| POST         | `/sair`                   | LoginController        | `logout`                |
| GET/POST/... | `/estoque`                | EstoqueController      | `estoque.*`             |
| GET          | `/vendas/exportar`        | VendasController       | `vendas.exportar`       |
| GET/POST/... | `/vendas`                 | VendasController       | `vendas.*`              |
| GET/POST/... | `/clientes`               | ClientesController     | `clientes.*`            |
| GET          | `/categorias`             | CategoriasController   | `categorias.index`      |
| POST         | `/categorias`             | CategoriasController   | `categorias.store`      |
| PUT          | `/categorias/{categoria}` | CategoriasController   | `categorias.update`     |
| DELETE       | `/categorias/{categoria}` | CategoriasController   | `categorias.destroy`    |
| GET/POST/... | `/usuarios`               | UsuariosController     | `usuarios.*` (admin)    |

---

## Comandos Artisan Úteis

```bash
# Executar migrations com seed
php artisan migrate:fresh --seed

# Executar apenas o seeder de roles/permissões
php artisan db:seed --class=RolesPermissoesSeeder

# Executar testes
php artisan test

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Gerar chave da aplicação
php artisan key:generate
```

---

## Seeders Disponíveis

| Seeder                   | Descrição                                             |
|--------------------------|-------------------------------------------------------|
| `RolesPermissoesSeeder`  | Cria roles (admin, editor, usuario) e 20 permissões   |
| `UsuariosSeeder`         | Cria 3 usuários padrão (um por role)                  |
| `ProdutosSeeder`         | Cria categorias e produtos de exemplo                 |
| `VendasSeeder`           | Cria clientes e vendas de exemplo                     |

---

## Factories

| Factory          | Modelo    |
|------------------|-----------|
| `UserFactory`    | `User`    |
| `ClienteFactory` | `Cliente` |
| `ProdutoFactory` | `Produto` |

---

## Service Layer

### `EstoqueService`

| Método       | Descrição                                                        |
|--------------|------------------------------------------------------------------|
| `criar()`    | Cria produto + registra movimentação de entrada se qtd > 0      |
| `atualizar()`| Atualiza produto + registra ajuste se quantidade mudou          |
| `movimentar()`| Registra entrada ou saída avulsa com atualização de estoque    |

### `VendaService`

| Método        | Descrição                                                       |
|---------------|-----------------------------------------------------------------|
| `criar()`     | Cria venda, itens, calcula totais, baixa estoque (se concluída)|
| `atualizar()` | Atualiza venda com recálculo de estoque e totais               |
| `cancelar()`  | Cancela venda e repõe estoque dos itens                        |

---

## Componentes Vue.js Globais (`resources/js/Components/`)

| Componente       | Props principais                        | Uso                              |
|------------------|-----------------------------------------|----------------------------------|
| `Botao.vue`      | `variant`, `carregando`, `disabled`     | Botões da aplicação              |
| `InputField.vue` | `label`, `modelValue`, `erro`, `type`  | Campos de formulário             |
| `SelectField.vue`| `label`, `modelValue`, `options`, `erro`| Selects de formulário           |
| `Modal.vue`      | `aberto`, `titulo`                      | Modais em geral                  |
| `Badge.vue`      | `variant` (verde, amarelo, vermelho...) | Badges de status                 |
| `NavItem.vue`    | `href`, `active`, `icon`               | Itens do menu lateral            |
| `Paginacao.vue`  | `links` (array de links Inertia)       | Paginação de listas              |

---

## Validação

Todos os endpoints utilizam **Form Requests** dedicados:

- `App\Http\Requests\Auth\LoginRequest`
- `App\Http\Requests\Auth\CadastroRequest`
- `App\Http\Requests\Auth\EsqueciSenhaRequest`
- `App\Http\Requests\Auth\RedefinirSenhaRequest`
- `App\Http\Requests\StoreProdutoRequest`
- `App\Http\Requests\UpdateProdutoRequest`
- `App\Http\Requests\StoreVendaRequest`
- `App\Http\Requests\UpdateVendaRequest`
- `App\Http\Requests\StoreClienteRequest`
- `App\Http\Requests\UpdateClienteRequest`
- `App\Http\Requests\StoreCategoriaRequest`
- `App\Http\Requests\UpdateCategoriaRequest`
- `App\Http\Requests\StoreUsuarioRequest`
- `App\Http\Requests\UpdateUsuarioRequest`

---

## Padrão de Resposta Inertia

Todos os controllers retornam `Inertia::render('Modulo/Pagina', [...])`.  
Os dados são compartilhados via `HandleInertiaRequests` middleware (user, auth, ziggy).

Erros de validação são automaticamente injetados no `$page.props.errors` no Vue.  
Flash messages (`sucesso`) ficam em `$page.props.flash`.

---

## Variáveis Docker

O `docker-compose.yml` define 4 services:

| Service      | Container           | Porta        |
|--------------|---------------------|--------------|
| `app`        | `laravel-app`       | (interno)    |
| `webserver`  | `laravel-webserver` | 8081:80      |
| `db`         | `laravel-db`        | 3306:3306    |
| `node`       | `laravel-node`      | 5173:5173    |

Volumes nomeados para performance no Windows/WSL2:
- `vendor_cache` → `/var/www/vendor` (PHP dependencies)
- `node_modules_cache` → `/var/www/node_modules` (JS dependencies)
- `mysql_data` → dados persistentes do MySQL

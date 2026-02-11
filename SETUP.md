# Desafio Bug Hunt - Laravel

O Desafio é composto por 2 partes:

1. Identificar problemas no código
2. Corrigir os problemas no banco de dados

## Parte 1: O Repositório "Bug Hunt" (Legado)

O objetivo aqui é avaliar a capacidade de leitura de código, depuração e conhecimento de segurança/performance.

### O Cenário

Um sistema simples de Gestão de Chamados (Tickets) que está apresentando comportamentos inesperados e falhas de segurança.

### Defeitos Propositais para Corrigir:

- **N+1 Query**: No Controller, carregar a lista de tickets sem o with('user'), causando lentidão.

- **Falha de Segurança (Mass Assignment)**: Deixar o array $guarded vazio ou $fillable amplo demais em um model sensível.

- **Inertia State Mix-up**: Enviar dados desnecessários para o frontend via Inertia::render, expondo hashes de senha ou dados sensíveis no JSON da página.

- **Bug de API**: Um endpoint que deveria retornar JSON, mas retorna um erro HTML 500 por falta de tratamento de exceção (Try/Catch ausente).

- **Validação Fraca**: Um formulário que aceita strings onde deveriam ser IDs numéricos, quebrando a integridade do banco.

**Instrução para o candidato**: "Identifique e corrija ao menos 4 bugs críticos e refatore a consulta de listagem para otimizar o banco de dados."

---

## Setup do Ambiente Docker

Este projeto utiliza Docker com uma arquitetura completa para desenvolvimento Laravel.

### Arquitetura

A stack é composta por 4 serviços conectados via **network bridge**:

- **app** (PHP 8.3-FPM): Executa a aplicação Laravel
- **webserver** (Nginx): Servidor web para servir a aplicação
- **db** (MySQL 8.0): Banco de dados
- **node** (Node 20): Compila assets com Vite (HMR habilitado)

### Pré-requisitos

- Docker e Docker Compose instalados
- Just (task runner) - Opcional, mas recomendado
- **Windows**: Git Bash instalado e adicionado ao PATH (obrigatório para uso do `just`)
  ```bash
  # Ubuntu/Debian
  curl --proto '=https' --tlsv1.2 -sSf https://just.systems/install.sh | bash -s -- --to /usr/local/bin

  # macOS
  brew install just
  ```

### Primeira Execução

1. **Subir os containers**:
   ```bash
   just up
   # ou
   docker-compose up -d
   ```

2. **Instalar Laravel** (primeira vez):
   ```bash
   just install
   ```

   Este comando irá:
   - Criar um novo projeto Laravel em `/src`
   - Configurar o `.env` para usar o banco Docker
   - Gerar a application key
   - Executar as migrations
   - Instalar dependências npm

3. **Acessar a aplicação**:
   - Frontend: http://localhost:8081
   - Vite Dev Server: http://localhost:5173
   - MySQL: localhost:3306 (user: laravel, password: secret)

### Comandos Úteis

#### Docker Management
```bash
just up          # Inicia containers
just down        # Para containers
just restart     # Reinicia containers
just ps          # Status dos containers
just logs        # Ver logs (all services)
just logs app    # Ver logs de serviço específico
just rebuild     # Rebuild completo dos containers
```

#### Laravel
```bash
just artisan migrate              # Rodar migrations
just artisan make:model Post      # Criar model
just composer require package     # Instalar pacote PHP
just test                         # Rodar testes
```

#### Database
```bash
just migrate     # Executar migrations
just fresh       # Fresh migration com seed
just rollback    # Rollback última migration
just seed        # Executar seeders
just mysql       # Acessar MySQL CLI
```

#### Frontend (Vite)
```bash
just npm install          # Instalar dependências
just npm run build        # Build de produção
# O Vite dev já roda automaticamente com HMR
```

#### Shell Access
```bash
just shell app      # Acessar shell do container PHP
just shell node     # Acessar shell do container Node
just bash           # Bash no container app
```

#### Manutenção
```bash
just clear       # Limpar caches do Laravel
just optimize    # Otimizar para produção
just clean       # Limpar tudo (containers + volumes + deps)
just reset       # Reset completo (clean + rebuild)
```

### Estrutura de Diretórios

```
.
├── docker/
│   ├── nginx/
│   │   ├── Dockerfile
│   │   └── default.conf
│   ├── php/
│   │   ├── Dockerfile
│   │   └── php.ini
│   ├── mysql/
│   │   └── init/
│   └── node/
│       └── Dockerfile
├── src/                    # Código Laravel (criado após just install)
├── docker-compose.yml      # Orquestração dos serviços
├── justfile               # Comandos facilitadores
└── Readme.md
```

### Configuração do Vite

O Vite está configurado para funcionar com Hot Module Replacement (HMR). Para usar no Laravel:

1. No seu `vite.config.js`, adicione:
   ```javascript
   export default defineConfig({
       server: {
           host: '0.0.0.0',
           port: 5173,
           hmr: {
               host: 'localhost'
           }
       }
   });
   ```

2. No `.env`:
   ```
   VITE_DEV_SERVER_HOST=0.0.0.0
   VITE_DEV_SERVER_PORT=5173
   ```

### Troubleshooting

**Containers não sobem:**
```bash
just logs        # Ver logs de erro
docker-compose ps  # Verificar status
```

**Erro de permissão no Laravel:**
```bash
just shell app
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

**MySQL não conecta:**
- Aguarde o healthcheck completar (~30s após `just up`)
- Verifique: `docker-compose logs db`

**Vite não atualiza:**
```bash
just logs node   # Ver logs do Vite
just npm install # Reinstalar dependências
```

### Próximos Passos

Após o setup, você pode começar a trabalhar no desafio:

1. Explore o código Laravel em `/src`
2. Identifique os bugs listados na Parte 1
3. Corrija os problemas encontrados
4. Execute os testes: `just test`

---

### Tecnologias

- PHP 8.3
- Laravel 11.x
- MySQL 8.0
- Node 20
- Vite
- Nginx
- Docker & Docker Compose
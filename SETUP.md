# Laravel Docker Setup - Guia de Configuração

## 🔧 Correções Aplicadas

### 1. Sistema de Permissões Multi-OS
- ✅ Script robusto de correção de permissões ([docker/php/fix-permissions.sh](docker/php/fix-permissions.sh))
- ✅ Entrypoint otimizado ([docker/php/entrypoint.sh](docker/php/entrypoint.sh))
- ✅ Funciona em **Linux, macOS e Windows**

### 2. Configuração de Banco de Dados
- ✅ MySQL configurado em vez de SQLite ([src/.env](src/.env))
- ✅ Conexão com container `db`

### 3. Vite para SPA
- ✅ HMR (Hot Module Replacement) configurado
- ✅ Polling ativo para Docker
- ✅ Porta 5173 exposta

## 🚀 Como Aplicar as Correções

### Opção 1: Setup Completo (Recomendado)

```bash
# 1. Parar containers
just down

# 2. Reconstruir com as novas configurações
just build

# 3. Iniciar containers
just up

# 4. Corrigir permissões
just fix-permissions

# 5. Rodar migrations
just migrate

# 6. Ver logs do Vite (opcional)
just watch node
```

### Opção 2: Rebuild Rápido

```bash
# Faz tudo de uma vez: down + build + up
just rebuild

# Depois corrigir permissões e migrations
just fix-permissions
just migrate
```

## 📝 Comandos Úteis

### Gerenciamento de Containers
```bash
just up          # Iniciar containers
just down        # Parar containers
just restart     # Reiniciar containers
just ps          # Status dos containers
just logs        # Ver logs de todos os serviços
```

### Laravel
```bash
just artisan [comando]      # Executar comandos artisan
just migrate                # Rodar migrations
just fresh                  # Resetar banco com seed
just fix-permissions        # Corrigir permissões
```

### Frontend (Vite)
```bash
just npm install           # Instalar dependências
just npm run dev          # Iniciar Vite (já roda automaticamente)
just npm run build        # Build de produção
```

### Acesso Shell
```bash
just bash                 # Shell do container PHP
just shell app            # Shell em qualquer container
just mysql                # MySQL CLI
```

## 🔍 Verificação

Após executar o setup, verifique:

1. **Laravel funcionando**: http://localhost
2. **Vite HMR ativo**: Porta 5173 deve estar respondendo
3. **Banco de dados**: Tabelas criadas (incluindo `sessions`)
4. **Logs funcionando**: Sem erros de permissão

## 🐛 Troubleshooting

### Erro de Permissão
```bash
just fix-permissions
```

### Tabela não existe
```bash
just migrate
```

### Vite não está atualizando
```bash
just restart-service node
```

### Limpar tudo e começar do zero
```bash
just clean    # Remove containers e dependências
just rebuild  # Reconstrói tudo
just fix-permissions
just migrate
```

## 📦 Estrutura de Permissões

O script `fix-permissions.sh` garante que:
- **Usuário**: www (UID 1000, GID 1000)
- **Diretórios**: 775 (rwxrwxr-x)
- **Arquivos**: 664 (rw-rw-r--)
- **Diretórios críticos com escrita**:
  - `storage/*`
  - `bootstrap/cache`

## 🌐 Compatibilidade

| OS      | Status | Notas                          |
|---------|--------|--------------------------------|
| Linux   | ✅     | Testado                        |
| macOS   | ✅     | Volumes funcionam nativamente  |
| Windows | ✅     | Via Docker Desktop WSL2        |

## 📌 Portas Expostas

- **80**: Nginx (Laravel)
- **443**: Nginx SSL
- **3306**: MySQL
- **5173**: Vite HMR

---

**Próximos Passos**: Execute `just rebuild && just fix-permissions && just migrate`

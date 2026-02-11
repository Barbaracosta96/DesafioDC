# Guia do Justfile - Multiplataforma

Este justfile funciona automaticamente em **Linux**, **macOS** e **Windows**.

## 🔍 Detecção Automática de Sistema Operacional

O justfile detecta automaticamente seu SO e ajusta os comandos:

- **Linux**: Usa `sudo docker-compose` (padrão)
- **macOS**: Usa `docker-compose` (sem sudo)
- **Windows**: Usa `docker-compose` (sem sudo)

### Verificar sua configuração

```bash
just info
```

Isso mostra:
- Sistema operacional detectado
- Comando Docker Compose que será usado
- Status dos containers

## ⚙️ Configuração sem SUDO (Linux)

Se você configurou o Docker para rodar sem sudo no Linux, pode modificar o justfile:

### Opção 1: Editar o justfile

Altere a linha 14-16 de:
```just
} else {
    "sudo docker-compose"
}
```

Para:
```just
} else {
    "docker-compose"
}
```

### Opção 2: Adicionar usuário ao grupo docker

```bash
# Adicionar seu usuário ao grupo docker
sudo usermod -aG docker $USER

# Fazer logout e login novamente ou executar:
newgrp docker

# Testar
docker ps
```

Após isso, você pode usar o justfile sem sudo!

## 📋 Comandos Principais

### Gestão Docker

| Comando | Descrição |
|---------|-----------|
| `just up` | Inicia todos os containers |
| `just down` | Para todos os containers |
| `just restart` | Reinicia containers |
| `just ps` | Mostra status dos containers |
| `just logs` | Ver logs em tempo real |
| `just logs app` | Ver logs de um serviço específico |
| `just rebuild` | Rebuild completo dos containers |

### Setup Laravel

| Comando | Descrição |
|---------|-----------|
| `just install` | Instalação completa (primeira vez) |
| `just setup` | Setup rápido (se Laravel já existe) |

### Comandos Laravel

| Comando | Exemplo |
|---------|---------|
| `just artisan` | `just artisan migrate` |
| `just composer` | `just composer require package` |
| `just npm` | `just npm install` |
| `just migrate` | Executar migrations |
| `just fresh` | Fresh migration com seed |
| `just seed` | Executar seeders |
| `just test` | Rodar testes PHPUnit |

### Geradores Laravel

| Comando | Exemplo |
|---------|---------|
| `just make-migration` | `just make-migration create_posts_table` |
| `just make-model` | `just make-model Post -m` |
| `just make-controller` | `just make-controller PostController` |

### Acesso Shell

| Comando | Descrição |
|---------|-----------|
| `just shell app` | Acessar shell do container PHP |
| `just shell node` | Acessar shell do container Node |
| `just bash` | Bash no container app |
| `just mysql` | CLI MySQL (user: laravel) |
| `just mysql-root` | CLI MySQL (user: root) |

### Desenvolvimento

| Comando | Descrição |
|---------|-----------|
| `just dev` | Iniciar e ver logs |
| `just watch app` | Watch logs de um serviço |
| `just clear` | Limpar caches Laravel |
| `just pint` | Formatar código (Laravel Pint) |
| `just fix-permissions` | Corrigir permissões (Linux) |

### Manutenção

| Comando | Descrição |
|---------|-----------|
| `just clean` | Limpar tudo (containers + volumes) |
| `just reset` | Reset completo |
| `just prune` | Remover recursos Docker não usados |

## 🖥️ Uso por Sistema Operacional

### Linux

```bash
# Primeiro uso
just up
just install

# Desenvolvimento diário
just dev
just artisan migrate
just test

# Se tiver problema de permissão
just fix-permissions
```

### macOS

```bash
# Mesmo workflow do Linux
just up
just install
just dev

# fix-permissions não é necessário no macOS
```

### Windows

#### PowerShell
> [!IMPORTANT]
> Para usar o `just` no PowerShell, você deve ter o **Git Bash** instalado e adicionado ao seu PATH (`C:\Program Files\Git\bin`).
> Caso contrário, use o terminal **Git Bash**.

```powershell
# Primeiro uso
just up
just install

# Desenvolvimento
just dev
just artisan migrate
```

#### Git Bash / WSL
```bash
# Funciona igual ao Linux
just up
just install
just dev
```

## 🚀 Workflow Completo

### 1. Primeira vez (qualquer SO)

```bash
# Iniciar containers
just up

# Aguardar containers subirem (~30s)
just ps

# Instalar Laravel
just install

# Acessar: http://localhost
```

### 2. Desenvolvimento diário

```bash
# Iniciar ambiente
just dev

# Em outro terminal, executar comandos
just artisan make:model Post -m
just migrate
just npm run build
just test
```

### 3. Quando terminar

```bash
just down
```

## 🔧 Troubleshooting

### Erro: "sudo: a password is required"

**Solução 1**: Digite a senha quando solicitado

**Solução 2**: Configure Docker sem sudo (ver seção acima)

### Windows: Erro de permissão

No Windows, execute PowerShell ou Git Bash como **Administrador**.

### macOS: Docker não encontrado

Certifique-se que Docker Desktop está rodando:
```bash
open -a Docker
```

### Linux: Permissões em src/

```bash
just fix-permissions
```

## 📝 Customização

### Alterar porta padrão

Edite `docker-compose.yml`:
```yaml
webserver:
  ports:
    - "8080:80"  # Altere 8080 para porta desejada
```

### Usar PostgreSQL ao invés de MySQL

1. Edite `docker-compose.yml`
2. Substitua serviço `db` por PostgreSQL
3. Atualize variáveis de ambiente

## 💡 Dicas

1. **Ver todos comandos disponíveis**:
   ```bash
   just --list
   # ou
   just help
   ```

2. **Auto-complete** (Bash/Zsh):
   ```bash
   # Adicione ao ~/.bashrc ou ~/.zshrc
   eval "$(just --completions bash)"  # para bash
   eval "$(just --completions zsh)"   # para zsh
   ```

3. **Alias úteis** (opcional):
   ```bash
   # Adicione ao ~/.bashrc ou ~/.zshrc
   alias jup="just up"
   alias jdown="just down"
   alias jdev="just dev"
   alias jart="just artisan"
   ```

## 📚 Mais Recursos

- [Just Documentation](https://just.systems/)
- [Docker Documentation](https://docs.docker.com/)
- [Laravel Documentation](https://laravel.com/docs)

---

**Compatibilidade Testada**:
- ✅ Linux (Ubuntu 20.04+, Debian, Arch)
- ✅ macOS (Intel e Apple Silicon)
- ✅ Windows 10/11 (PowerShell, Git Bash, WSL2)

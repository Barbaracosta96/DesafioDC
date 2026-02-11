# 🚀 Projeto NewSDC - Gestão CEDEC

Este projeto faz parte da iniciativa de modernização e renovação do ecossistema da **Defesa Civil (CEDEC-MG)**. Desenvolvido com uma arquitetura moderna em **PHP 8.4** e **Laravel**, focado em descentralização, performance e escalabilidade. 🛠️

---

## 💻 Como Executar (Git Bash)

Para rodar o ambiente localmente utilizando o terminal integrado, siga os passos abaixo:

1. **Abra o seu terminal** (Git Bash recomendado).
2. **Navegue até a pasta de infraestrutura**:
   ```bash
   cd docker/
   ```

# 🛠️ Orquestração com Justfile

Para facilitar o fluxo de trabalho e evitar comandos extensos de Docker, utilizamos o Justfile. Ele funciona como um orquestrador de tarefas simplificado. 🎯

> [!IMPORTANT]
> **Pré-requisito**: É necessário ter o interpretador binário do `just` instalado e configurado no seu PATH.

## 📜 Comandos Disponíveis

Execute os comandos abaixo diretamente no diretório raiz:

| Comando | Descrição |
| :--- | :--- |
| `just list` | ✨ Lista todos os comandos configurados no projeto. |
| `just build` | 🏗️ Realiza o build das imagens e sobe os containers do Docker. |
| `just shell app` | 🐚 Abre o terminal interativo dentro do container da aplicação (Laravel). |
| `just rebuild` | 🔄 Remove os volumes, reconstrói as imagens e sobe o ambiente do zero. |

## 🐳 Ambiente Docker

Toda a configuração de infraestrutura (Nginx, MySQL, PHP-FPM) está centralizada na pasta do docker.

- **Stack**: PHP 8.4, Docker, Jenkins (CI/CD) e MySQL.
- **Frontend**: Vue.js com Inertia.js.

## 📋 Requisitos e Notas

- Certifique-se de que a porta **80** (ou a configurada no `.env`) esteja livre.
- As assinaturas digitais e documentos formais do projeto seguem o padrão da PUC Universidade.

---

✨ **Desenvolvido por Matheus Estrela** - Foco em produtividade e alta performance.

#!/bin/bash
# run_all_tests.sh - Script Principal de Testes
# Executa todos os testes do desafio tecnico

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/helpers.sh"

# ============================================
# CONFIGURACAO
# ============================================

# Contadores globais
TOTAL_SUITES=0
PASSED_SUITES=0
FAILED_SUITES=0

# Lista de testes
TESTS=(
    "test_auth.sh:Autenticacao Completa"
    "test_modules.sh:Modulos (Estoque/Vendas)"
    "test_dashboard.sh:Painel Administrativo"
    "test_acl.sh:Gestao de Acesso (ACL)"
    "test_crud.sh:CRUD RESTful"
)

# ============================================
# FUNCOES
# ============================================

print_banner() {
    echo ""
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}   SUITE DE TESTES - DESAFIO TECNICO   ${NC}"
    echo -e "${BLUE}========================================${NC}"
    echo ""
    echo -e "Data: $(date '+%Y-%m-%d %H:%M:%S')"
    echo -e "URL Base: $BASE_URL"
    echo ""
}

print_help() {
    echo "Uso: $0 [opcoes] [teste]"
    echo ""
    echo "Opcoes:"
    echo "  -h, --help      Mostra esta ajuda"
    echo "  -v, --verbose   Modo verboso"
    echo "  -l, --list      Lista testes disponiveis"
    echo "  -u, --url URL   Define URL base da API"
    echo ""
    echo "Testes disponiveis:"
    echo "  auth            Testes de autenticacao"
    echo "  modules         Testes de modulos (estoque/vendas)"
    echo "  dashboard       Testes do painel administrativo"
    echo "  acl             Testes de gestao de acesso"
    echo "  crud            Testes de CRUD RESTful"
    echo "  all             Executa todos os testes (padrao)"
    echo ""
    echo "Exemplos:"
    echo "  $0                    # Executa todos os testes"
    echo "  $0 auth               # Executa apenas testes de auth"
    echo "  $0 -v crud            # Executa testes CRUD em modo verboso"
    echo "  $0 -u http://api:8080 # Define URL customizada"
    echo ""
}

list_tests() {
    echo "Testes disponiveis:"
    echo ""
    for test in "${TESTS[@]}"; do
        local file=$(echo "$test" | cut -d: -f1)
        local desc=$(echo "$test" | cut -d: -f2)
        local status="OK"

        if [ ! -f "$SCRIPT_DIR/$file" ]; then
            status="MISSING"
        fi

        printf "  %-20s %-30s [%s]\n" "$file" "$desc" "$status"
    done
    echo ""
}

run_test_file() {
    local file="$1"
    local desc="$2"

    echo ""
    echo -e "${BLUE}----------------------------------------${NC}"
    echo -e "${BLUE}Executando: $desc${NC}"
    echo -e "${BLUE}Arquivo: $file${NC}"
    echo -e "${BLUE}----------------------------------------${NC}"
    echo ""

    TOTAL_SUITES=$((TOTAL_SUITES + 1))

    if [ ! -f "$SCRIPT_DIR/$file" ]; then
        echo -e "${RED}ERRO: Arquivo $file nao encontrado${NC}"
        FAILED_SUITES=$((FAILED_SUITES + 1))
        return 1
    fi

    # Executa o teste
    bash "$SCRIPT_DIR/$file"
    local exit_code=$?

    if [ $exit_code -eq 0 ]; then
        PASSED_SUITES=$((PASSED_SUITES + 1))
        echo -e "${GREEN}Suite $desc: PASSOU${NC}"
    else
        FAILED_SUITES=$((FAILED_SUITES + 1))
        echo -e "${RED}Suite $desc: FALHOU${NC}"
    fi

    return $exit_code
}

run_single_test() {
    local test_name="$1"

    case "$test_name" in
        "auth")
            run_test_file "test_auth.sh" "Autenticacao Completa"
            ;;
        "modules")
            run_test_file "test_modules.sh" "Modulos (Estoque/Vendas)"
            ;;
        "dashboard")
            run_test_file "test_dashboard.sh" "Painel Administrativo"
            ;;
        "acl")
            run_test_file "test_acl.sh" "Gestao de Acesso (ACL)"
            ;;
        "crud")
            run_test_file "test_crud.sh" "CRUD RESTful"
            ;;
        *)
            echo -e "${RED}Teste desconhecido: $test_name${NC}"
            echo "Use --list para ver testes disponiveis"
            exit 1
            ;;
    esac
}

run_all_tests() {
    for test in "${TESTS[@]}"; do
        local file=$(echo "$test" | cut -d: -f1)
        local desc=$(echo "$test" | cut -d: -f2)

        run_test_file "$file" "$desc"
    done
}

print_final_summary() {
    echo ""
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}   RESUMO FINAL DA SUITE DE TESTES     ${NC}"
    echo -e "${BLUE}========================================${NC}"
    echo ""
    echo -e "Total de suites: $TOTAL_SUITES"
    echo -e "${GREEN}Passaram: $PASSED_SUITES${NC}"
    echo -e "${RED}Falharam: $FAILED_SUITES${NC}"
    echo ""

    if [ $FAILED_SUITES -eq 0 ]; then
        echo -e "${GREEN}========================================${NC}"
        echo -e "${GREEN}   TODOS OS TESTES PASSARAM!           ${NC}"
        echo -e "${GREEN}========================================${NC}"
        return 0
    else
        echo -e "${RED}========================================${NC}"
        echo -e "${RED}   ALGUNS TESTES FALHARAM!             ${NC}"
        echo -e "${RED}========================================${NC}"
        return 1
    fi
}

# ============================================
# MAIN
# ============================================

main() {
    local run_tests="all"

    # Parse argumentos
    while [[ $# -gt 0 ]]; do
        case "$1" in
            -h|--help)
                print_help
                exit 0
                ;;
            -v|--verbose)
                export VERBOSE="true"
                shift
                ;;
            -l|--list)
                list_tests
                exit 0
                ;;
            -u|--url)
                export BASE_URL="$2"
                export API_URL="${BASE_URL}/api"
                shift 2
                ;;
            *)
                run_tests="$1"
                shift
                ;;
        esac
    done

    print_banner

    # Verifica dependencias
    check_dependencies

    # Verifica se API esta online
    if ! check_api_health; then
        echo ""
        echo -e "${RED}API nao esta acessivel. Verifique:${NC}"
        echo "  1. Os containers estao rodando? (docker-compose ps)"
        echo "  2. A URL esta correta? (BASE_URL=$BASE_URL)"
        echo "  3. A porta esta correta? (padrao: 8081)"
        echo ""
        exit 1
    fi

    echo ""
    echo -e "${BLUE}Iniciando testes...${NC}"
    echo ""

    # Executa testes
    if [ "$run_tests" = "all" ]; then
        run_all_tests
    else
        run_single_test "$run_tests"
    fi

    # Resumo final
    print_final_summary
    exit $?
}

# Executa
main "$@"

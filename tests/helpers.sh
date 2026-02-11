#!/bin/bash
# helpers.sh - Funcoes auxiliares para testes

# Configuracao
BASE_URL="${BASE_URL:-http://localhost:8081}"
API_URL="${BASE_URL}/api"
VERBOSE="${VERBOSE:-false}"

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Contadores
TESTS_PASSED=0
TESTS_FAILED=0
TESTS_TOTAL=0

# Token de autenticacao (preenchido apos login)
AUTH_TOKEN=""

# ============================================
# FUNCOES DE OUTPUT
# ============================================

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[PASS]${NC} $1"
}

log_error() {
    echo -e "${RED}[FAIL]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_section() {
    echo ""
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}========================================${NC}"
    echo ""
}

# ============================================
# FUNCOES DE REQUEST HTTP
# ============================================

# Faz requisicao HTTP e retorna body
# Uso: response=$(make_request GET /api/users)
make_request() {
    local method="$1"
    local endpoint="$2"
    local data="$3"
    local extra_headers="$4"

    local url="${API_URL}${endpoint}"
    local headers="-H 'Content-Type: application/json' -H 'Accept: application/json'"

    # Adiciona token se disponivel
    if [ -n "$AUTH_TOKEN" ]; then
        headers="$headers -H 'Authorization: Bearer $AUTH_TOKEN'"
    fi

    # Adiciona headers extras
    if [ -n "$extra_headers" ]; then
        headers="$headers $extra_headers"
    fi

    local cmd="curl -s -X $method $headers"

    if [ -n "$data" ]; then
        cmd="$cmd -d '$data'"
    fi

    cmd="$cmd '$url'"

    if [ "$VERBOSE" = "true" ]; then
        log_info "Request: $method $url"
        [ -n "$data" ] && log_info "Body: $data"
    fi

    eval $cmd
}

# Faz requisicao e retorna status code
# Uso: status=$(get_status GET /api/users)
get_status() {
    local method="$1"
    local endpoint="$2"
    local data="$3"

    local url="${API_URL}${endpoint}"
    local headers="-H 'Content-Type: application/json' -H 'Accept: application/json'"

    if [ -n "$AUTH_TOKEN" ]; then
        headers="$headers -H 'Authorization: Bearer $AUTH_TOKEN'"
    fi

    local cmd="curl -s -o /dev/null -w '%{http_code}' -X $method $headers"

    if [ -n "$data" ]; then
        cmd="$cmd -d '$data'"
    fi

    cmd="$cmd '$url'"

    eval $cmd
}

# Faz requisicao e retorna body + status
# Uso: result=$(make_request_full GET /api/users)
# Body na primeira linha, status na segunda
make_request_full() {
    local method="$1"
    local endpoint="$2"
    local data="$3"

    local url="${API_URL}${endpoint}"
    local headers="-H 'Content-Type: application/json' -H 'Accept: application/json'"

    if [ -n "$AUTH_TOKEN" ]; then
        headers="$headers -H 'Authorization: Bearer $AUTH_TOKEN'"
    fi

    local cmd="curl -s -w '\n%{http_code}' -X $method $headers"

    if [ -n "$data" ]; then
        cmd="$cmd -d '$data'"
    fi

    cmd="$cmd '$url'"

    eval $cmd
}

# ============================================
# FUNCOES DE ASSERT
# ============================================

# Incrementa contador de testes
increment_test() {
    TESTS_TOTAL=$((TESTS_TOTAL + 1))
}

# Marca teste como passou
test_pass() {
    local message="$1"
    TESTS_PASSED=$((TESTS_PASSED + 1))
    log_success "$message"
}

# Marca teste como falhou
test_fail() {
    local message="$1"
    TESTS_FAILED=$((TESTS_FAILED + 1))
    log_error "$message"
}

# Verifica status HTTP
# Uso: assert_status 200 "$status" "Deve retornar 200"
assert_status() {
    local expected="$1"
    local actual="$2"
    local message="$3"

    increment_test

    if [ "$actual" = "$expected" ]; then
        test_pass "$message (status: $actual)"
        return 0
    else
        test_fail "$message (esperado: $expected, recebido: $actual)"
        return 1
    fi
}

# Verifica se resposta contem campo JSON
# Uso: assert_json_has_field "$response" "token" "Deve conter token"
assert_json_has_field() {
    local json="$1"
    local field="$2"
    local message="$3"

    increment_test

    if echo "$json" | jq -e ".$field" > /dev/null 2>&1; then
        test_pass "$message"
        return 0
    else
        test_fail "$message (campo '$field' nao encontrado)"
        return 1
    fi
}

# Verifica se resposta eh array
# Uso: assert_is_array "$response" "Deve ser array"
assert_is_array() {
    local json="$1"
    local message="$2"

    increment_test

    if echo "$json" | jq -e 'if type == "array" then true else false end' 2>/dev/null | grep -q "true"; then
        test_pass "$message"
        return 0
    else
        test_fail "$message (resposta nao eh array)"
        return 1
    fi
}

# Verifica se resposta contem data paginada (Laravel)
# Uso: assert_paginated "$response" "Deve ser paginado"
assert_paginated() {
    local json="$1"
    local message="$2"

    increment_test

    if echo "$json" | jq -e '.data and .meta' > /dev/null 2>&1 || \
       echo "$json" | jq -e '.data and .current_page' > /dev/null 2>&1; then
        test_pass "$message"
        return 0
    else
        test_fail "$message (resposta nao eh paginada)"
        return 1
    fi
}

# Verifica valor de campo JSON
# Uso: assert_json_value "$response" "status" "success" "Status deve ser success"
assert_json_value() {
    local json="$1"
    local field="$2"
    local expected="$3"
    local message="$4"

    increment_test

    local actual=$(echo "$json" | jq -r ".$field" 2>/dev/null)

    if [ "$actual" = "$expected" ]; then
        test_pass "$message"
        return 0
    else
        test_fail "$message (esperado: $expected, recebido: $actual)"
        return 1
    fi
}

# Verifica se string nao esta vazia
# Uso: assert_not_empty "$value" "Token nao deve ser vazio"
assert_not_empty() {
    local value="$1"
    local message="$2"

    increment_test

    if [ -n "$value" ] && [ "$value" != "null" ]; then
        test_pass "$message"
        return 0
    else
        test_fail "$message (valor vazio ou null)"
        return 1
    fi
}

# ============================================
# FUNCOES DE AUTENTICACAO
# ============================================

# Registra usuario e retorna token
# Uso: register_user "test@test.com" "password123" "Test User"
register_user() {
    local email="$1"
    local password="$2"
    local name="$3"

    local data="{\"email\":\"$email\",\"password\":\"$password\",\"password_confirmation\":\"$password\",\"name\":\"$name\"}"
    make_request POST "/register" "$data"
}

# Faz login e salva token
# Uso: login_user "test@test.com" "password123"
login_user() {
    local email="$1"
    local password="$2"

    local data="{\"email\":\"$email\",\"password\":\"$password\"}"
    local response=$(make_request POST "/login" "$data")

    # Tenta extrair token
    AUTH_TOKEN=$(echo "$response" | jq -r '.token // .access_token // .data.token // empty' 2>/dev/null)

    echo "$response"
}

# Faz logout
logout_user() {
    make_request POST "/logout"
    AUTH_TOKEN=""
}

# ============================================
# FUNCOES DE RELATORIO
# ============================================

# Imprime resumo dos testes
print_summary() {
    echo ""
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}  RESUMO DOS TESTES${NC}"
    echo -e "${BLUE}========================================${NC}"
    echo ""
    echo -e "Total de testes: ${TESTS_TOTAL}"
    echo -e "${GREEN}Passou: ${TESTS_PASSED}${NC}"
    echo -e "${RED}Falhou: ${TESTS_FAILED}${NC}"
    echo ""

    if [ $TESTS_FAILED -eq 0 ]; then
        echo -e "${GREEN}TODOS OS TESTES PASSARAM!${NC}"
        return 0
    else
        echo -e "${RED}ALGUNS TESTES FALHARAM!${NC}"
        return 1
    fi
}

# Reseta contadores
reset_counters() {
    TESTS_PASSED=0
    TESTS_FAILED=0
    TESTS_TOTAL=0
    AUTH_TOKEN=""
}

# ============================================
# FUNCOES UTILITARIAS
# ============================================

# Gera email unico para testes
generate_test_email() {
    echo "test_$(date +%s)_$RANDOM@test.com"
}

# Gera dados de teste para produto
generate_product_data() {
    local name="${1:-Produto Teste}"
    local price="${2:-99.99}"
    local stock="${3:-100}"

    echo "{\"name\":\"$name\",\"price\":$price,\"stock\":$stock,\"description\":\"Descricao do produto\"}"
}

# Gera dados de teste para venda
generate_sale_data() {
    local product_id="${1:-1}"
    local quantity="${2:-1}"

    echo "{\"items\":[{\"product_id\":$product_id,\"quantity\":$quantity}]}"
}

# Verifica dependencias
check_dependencies() {
    local missing=0

    if ! command -v curl &> /dev/null; then
        log_error "curl nao encontrado. Instale com: apt install curl"
        missing=1
    fi

    if ! command -v jq &> /dev/null; then
        log_error "jq nao encontrado. Instale com: apt install jq"
        missing=1
    fi

    if [ $missing -eq 1 ]; then
        exit 1
    fi

    log_success "Dependencias verificadas: curl, jq"
}

# Verifica se API esta online
check_api_health() {
    log_info "Verificando conexao com API: $BASE_URL"

    local status=$(curl -s -o /dev/null -w '%{http_code}' "$BASE_URL" 2>/dev/null)

    if [ "$status" = "000" ]; then
        log_error "API nao esta acessivel em $BASE_URL"
        log_info "Verifique se os containers estao rodando: docker-compose ps"
        return 1
    fi

    log_success "API online (status: $status)"
    return 0
}

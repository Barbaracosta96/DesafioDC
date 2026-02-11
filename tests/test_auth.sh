#!/bin/bash
# test_auth.sh - Testes de Autenticacao Completa
# Testa: Cadastro, Login, Logout, Recuperacao de Senha

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/helpers.sh"

# ============================================
# CONFIGURACAO
# ============================================

TEST_EMAIL=$(generate_test_email)
TEST_PASSWORD="Password123!"
TEST_NAME="Usuario Teste"

# ============================================
# TESTES DE REGISTRO
# ============================================

test_register() {
    log_section "TESTES DE REGISTRO DE USUARIO"

    # Teste 1: Registro com dados validos
    log_info "Teste: Registro com dados validos"
    local data="{\"name\":\"$TEST_NAME\",\"email\":\"$TEST_EMAIL\",\"password\":\"$TEST_PASSWORD\",\"password_confirmation\":\"$TEST_PASSWORD\"}"
    local result=$(make_request_full POST "/register" "$data")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "201" "$status" "Registro deve retornar 201 Created"
    assert_json_has_field "$body" "user" "Resposta deve conter campo 'user'" || \
    assert_json_has_field "$body" "data" "Resposta deve conter campo 'data'" || \
    assert_json_has_field "$body" "id" "Resposta deve conter campo 'id'"

    # Teste 2: Registro com email duplicado
    log_info "Teste: Registro com email duplicado"
    local result2=$(make_request_full POST "/register" "$data")
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "422" "$status2" "Registro duplicado deve retornar 422 Unprocessable Entity"

    # Teste 3: Registro sem email
    log_info "Teste: Registro sem email"
    local data3="{\"name\":\"$TEST_NAME\",\"password\":\"$TEST_PASSWORD\",\"password_confirmation\":\"$TEST_PASSWORD\"}"
    local result3=$(make_request_full POST "/register" "$data3")
    local status3=$(echo "$result3" | tail -n 1)

    assert_status "422" "$status3" "Registro sem email deve retornar 422"

    # Teste 4: Registro com senha fraca
    log_info "Teste: Registro com senha fraca"
    local weak_email=$(generate_test_email)
    local data4="{\"name\":\"$TEST_NAME\",\"email\":\"$weak_email\",\"password\":\"123\",\"password_confirmation\":\"123\"}"
    local result4=$(make_request_full POST "/register" "$data4")
    local status4=$(echo "$result4" | tail -n 1)

    assert_status "422" "$status4" "Registro com senha fraca deve retornar 422"

    # Teste 5: Registro com senhas diferentes
    log_info "Teste: Registro com senhas diferentes"
    local diff_email=$(generate_test_email)
    local data5="{\"name\":\"$TEST_NAME\",\"email\":\"$diff_email\",\"password\":\"$TEST_PASSWORD\",\"password_confirmation\":\"OutraSenha123!\"}"
    local result5=$(make_request_full POST "/register" "$data5")
    local status5=$(echo "$result5" | tail -n 1)

    assert_status "422" "$status5" "Registro com senhas diferentes deve retornar 422"
}

# ============================================
# TESTES DE LOGIN
# ============================================

test_login() {
    log_section "TESTES DE LOGIN"

    # Teste 1: Login com credenciais validas
    log_info "Teste: Login com credenciais validas"
    local data="{\"email\":\"$TEST_EMAIL\",\"password\":\"$TEST_PASSWORD\"}"
    local result=$(make_request_full POST "/login" "$data")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Login valido deve retornar 200"

    # Extrai e salva token
    AUTH_TOKEN=$(echo "$body" | jq -r '.token // .access_token // .data.token // .data.access_token // empty' 2>/dev/null)
    assert_not_empty "$AUTH_TOKEN" "Login deve retornar token de acesso"

    # Teste 2: Login com email invalido
    log_info "Teste: Login com email invalido"
    local data2="{\"email\":\"naoexiste@test.com\",\"password\":\"$TEST_PASSWORD\"}"
    local result2=$(make_request_full POST "/login" "$data2")
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "401" "$status2" "Login com email invalido deve retornar 401" || \
    assert_status "422" "$status2" "Login com email invalido deve retornar 422"

    # Teste 3: Login com senha invalida
    log_info "Teste: Login com senha invalida"
    local data3="{\"email\":\"$TEST_EMAIL\",\"password\":\"SenhaErrada123!\"}"
    local result3=$(make_request_full POST "/login" "$data3")
    local status3=$(echo "$result3" | tail -n 1)

    assert_status "401" "$status3" "Login com senha invalida deve retornar 401" || \
    assert_status "422" "$status3" "Login com senha invalida deve retornar 422"

    # Teste 4: Login sem credenciais
    log_info "Teste: Login sem credenciais"
    local result4=$(make_request_full POST "/login" "{}")
    local status4=$(echo "$result4" | tail -n 1)

    assert_status "422" "$status4" "Login sem credenciais deve retornar 422"
}

# ============================================
# TESTES DE LOGOUT
# ============================================

test_logout() {
    log_section "TESTES DE LOGOUT"

    # Primeiro faz login para ter token
    log_info "Fazendo login para obter token..."
    local data="{\"email\":\"$TEST_EMAIL\",\"password\":\"$TEST_PASSWORD\"}"
    local login_result=$(make_request POST "/login" "$data")
    AUTH_TOKEN=$(echo "$login_result" | jq -r '.token // .access_token // .data.token // .data.access_token // empty' 2>/dev/null)

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        log_warn "Nao foi possivel obter token. Pulando testes de logout."
        return
    fi

    # Teste 1: Logout autenticado
    log_info "Teste: Logout com token valido"
    local result=$(make_request_full POST "/logout")
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Logout autenticado deve retornar 200" || \
    assert_status "204" "$status" "Logout autenticado deve retornar 204"

    # Teste 2: Tentar usar token apos logout
    log_info "Teste: Usar token apos logout"
    local result2=$(make_request_full GET "/user")
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "401" "$status2" "Token apos logout deve ser invalido (401)"

    # Limpa token
    AUTH_TOKEN=""

    # Teste 3: Logout sem autenticacao
    log_info "Teste: Logout sem token"
    local result3=$(make_request_full POST "/logout")
    local status3=$(echo "$result3" | tail -n 1)

    assert_status "401" "$status3" "Logout sem token deve retornar 401"
}

# ============================================
# TESTES DE RECUPERACAO DE SENHA
# ============================================

test_password_reset() {
    log_section "TESTES DE RECUPERACAO DE SENHA"

    # Teste 1: Solicitar reset com email valido
    log_info "Teste: Forgot password com email valido"
    local data="{\"email\":\"$TEST_EMAIL\"}"
    local result=$(make_request_full POST "/forgot-password" "$data")
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Forgot password deve retornar 200" || \
    assert_status "202" "$status" "Forgot password deve retornar 202"

    # Teste 2: Solicitar reset com email invalido
    log_info "Teste: Forgot password com email invalido"
    local data2="{\"email\":\"naoexiste@test.com\"}"
    local result2=$(make_request_full POST "/forgot-password" "$data2")
    local body2=$(echo "$result2" | head -n -1)
    local status2=$(echo "$result2" | tail -n 1)

    # Alguns sistemas retornam 200 mesmo para email invalido (seguranca)
    # Outros retornam 422
    if [ "$status2" = "200" ] || [ "$status2" = "422" ]; then
        increment_test
        test_pass "Forgot password com email invalido tratado corretamente (status: $status2)"
    else
        increment_test
        test_fail "Forgot password com email invalido deveria retornar 200 ou 422 (recebido: $status2)"
    fi

    # Teste 3: Solicitar reset sem email
    log_info "Teste: Forgot password sem email"
    local result3=$(make_request_full POST "/forgot-password" "{}")
    local status3=$(echo "$result3" | tail -n 1)

    assert_status "422" "$status3" "Forgot password sem email deve retornar 422"

    # Teste 4: Reset password endpoint (sem token real, espera 422)
    log_info "Teste: Reset password com token invalido"
    local data4="{\"email\":\"$TEST_EMAIL\",\"token\":\"token-invalido\",\"password\":\"NovaSenha123!\",\"password_confirmation\":\"NovaSenha123!\"}"
    local result4=$(make_request_full POST "/reset-password" "$data4")
    local status4=$(echo "$result4" | tail -n 1)

    assert_status "422" "$status4" "Reset password com token invalido deve retornar 422" || \
    assert_status "400" "$status4" "Reset password com token invalido deve retornar 400"
}

# ============================================
# TESTES DE USUARIO AUTENTICADO
# ============================================

test_authenticated_user() {
    log_section "TESTES DE USUARIO AUTENTICADO"

    # Faz login
    log_info "Fazendo login..."
    local data="{\"email\":\"$TEST_EMAIL\",\"password\":\"$TEST_PASSWORD\"}"
    local login_result=$(make_request POST "/login" "$data")
    AUTH_TOKEN=$(echo "$login_result" | jq -r '.token // .access_token // .data.token // .data.access_token // empty' 2>/dev/null)

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        log_warn "Nao foi possivel obter token. Pulando testes de usuario autenticado."
        return
    fi

    # Teste 1: Obter dados do usuario autenticado
    log_info "Teste: GET /user ou /me"
    local result=$(make_request_full GET "/user")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    if [ "$status" = "404" ]; then
        # Tenta endpoint alternativo
        result=$(make_request_full GET "/me")
        body=$(echo "$result" | head -n -1)
        status=$(echo "$result" | tail -n 1)
    fi

    assert_status "200" "$status" "GET usuario autenticado deve retornar 200"
    assert_json_has_field "$body" "email" "Resposta deve conter email do usuario" || \
    assert_json_has_field "$body" "data" "Resposta deve conter dados do usuario"

    # Teste 2: Sem autenticacao
    AUTH_TOKEN=""
    log_info "Teste: GET /user sem autenticacao"
    local result2=$(make_request_full GET "/user")
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "401" "$status2" "GET /user sem token deve retornar 401"
}

# ============================================
# MAIN
# ============================================

main() {
    log_section "TESTES DE AUTENTICACAO"

    check_dependencies
    if ! check_api_health; then
        exit 1
    fi

    reset_counters

    test_register
    test_login
    test_logout
    test_password_reset
    test_authenticated_user

    print_summary
    exit $?
}

# Executa se chamado diretamente
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main
fi

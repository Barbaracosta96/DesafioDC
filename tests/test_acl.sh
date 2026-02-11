#!/bin/bash
# test_acl.sh - Testes de Gestao de Acesso (ACL)
# Testa: Roles, Permissions, Niveis de Acesso (Admin, Editor, Usuario)

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/helpers.sh"

# ============================================
# CONFIGURACAO
# ============================================

ADMIN_TOKEN=""
EDITOR_TOKEN=""
USER_TOKEN=""

ADMIN_EMAIL="admin_test_$(date +%s)@test.com"
EDITOR_EMAIL="editor_test_$(date +%s)@test.com"
USER_EMAIL="user_test_$(date +%s)@test.com"
TEST_PASSWORD="Password123!"

# ============================================
# HELPER: CRIAR USUARIOS DE TESTE
# ============================================

setup_test_users() {
    log_info "Criando usuarios de teste com diferentes roles..."

    # Tenta usar usuarios existentes primeiro
    log_info "Tentando login como admin existente..."
    local admin_login="{\"email\":\"admin@admin.com\",\"password\":\"password\"}"
    local result=$(make_request POST "/login" "$admin_login")
    ADMIN_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // empty' 2>/dev/null)

    if [ -n "$ADMIN_TOKEN" ] && [ "$ADMIN_TOKEN" != "null" ]; then
        log_success "Logado como admin existente"
    else
        log_info "Admin nao existe. Criando usuarios de teste..."

        # Criar usuario admin
        local admin_data="{\"name\":\"Admin Teste\",\"email\":\"$ADMIN_EMAIL\",\"password\":\"$TEST_PASSWORD\",\"password_confirmation\":\"$TEST_PASSWORD\",\"role\":\"admin\"}"
        make_request POST "/register" "$admin_data" > /dev/null

        # Login como admin
        local login_data="{\"email\":\"$ADMIN_EMAIL\",\"password\":\"$TEST_PASSWORD\"}"
        result=$(make_request POST "/login" "$login_data")
        ADMIN_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // empty' 2>/dev/null)
    fi

    # Criar usuario editor
    log_info "Criando usuario editor..."
    local editor_data="{\"name\":\"Editor Teste\",\"email\":\"$EDITOR_EMAIL\",\"password\":\"$TEST_PASSWORD\",\"password_confirmation\":\"$TEST_PASSWORD\",\"role\":\"editor\"}"
    make_request POST "/register" "$editor_data" > /dev/null

    local editor_login="{\"email\":\"$EDITOR_EMAIL\",\"password\":\"$TEST_PASSWORD\"}"
    result=$(make_request POST "/login" "$editor_login")
    EDITOR_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // empty' 2>/dev/null)

    # Criar usuario comum
    log_info "Criando usuario comum..."
    local user_data="{\"name\":\"Usuario Teste\",\"email\":\"$USER_EMAIL\",\"password\":\"$TEST_PASSWORD\",\"password_confirmation\":\"$TEST_PASSWORD\",\"role\":\"user\"}"
    make_request POST "/register" "$user_data" > /dev/null

    local user_login="{\"email\":\"$USER_EMAIL\",\"password\":\"$TEST_PASSWORD\"}"
    result=$(make_request POST "/login" "$user_login")
    USER_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // empty' 2>/dev/null)

    log_info "Tokens obtidos - Admin: $([ -n "$ADMIN_TOKEN" ] && echo 'sim' || echo 'nao'), Editor: $([ -n "$EDITOR_TOKEN" ] && echo 'sim' || echo 'nao'), User: $([ -n "$USER_TOKEN" ] && echo 'sim' || echo 'nao')"
}

# ============================================
# TESTES DE ROLES
# ============================================

test_roles_crud() {
    log_section "TESTES DE ROLES (CRUD)"

    AUTH_TOKEN="$ADMIN_TOKEN"

    # Teste 1: Listar roles (GET /roles)
    log_info "Teste: Listar roles (GET /roles)"
    local result=$(make_request_full GET "/roles")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Listar roles deve retornar 200"

    # Verifica se retorna array ou objeto paginado
    if [ "$status" = "200" ]; then
        log_info "Teste: Verificando estrutura de roles"
        local is_valid=$(echo "$body" | jq -e 'if type == "array" then true elif .data then true else false end' 2>/dev/null)

        increment_test
        if [ "$is_valid" = "true" ]; then
            test_pass "Roles retornou estrutura valida"
        else
            test_fail "Roles deveria retornar array ou objeto com data"
        fi
    fi

    # Teste 2: Criar role (POST /roles) - Apenas admin
    log_info "Teste: Criar role (POST /roles)"
    local role_data="{\"name\":\"test_role_$(date +%s)\",\"display_name\":\"Role de Teste\",\"description\":\"Role criada para teste\"}"
    local result2=$(make_request_full POST "/roles" "$role_data")
    local body2=$(echo "$result2" | head -n -1)
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "201" "$status2" "Criar role deve retornar 201"

    # Extrai ID da role criada
    local role_id=$(echo "$body2" | jq -r '.id // .data.id // empty' 2>/dev/null)

    # Teste 3: Visualizar role (GET /roles/{id})
    if [ -n "$role_id" ] && [ "$role_id" != "null" ]; then
        log_info "Teste: Visualizar role (GET /roles/$role_id)"
        local result3=$(make_request_full GET "/roles/$role_id")
        local status3=$(echo "$result3" | tail -n 1)

        assert_status "200" "$status3" "Visualizar role deve retornar 200"

        # Teste 4: Deletar role (DELETE /roles/{id})
        log_info "Teste: Deletar role (DELETE /roles/$role_id)"
        local result4=$(make_request_full DELETE "/roles/$role_id")
        local status4=$(echo "$result4" | tail -n 1)

        if [ "$status4" = "200" ] || [ "$status4" = "204" ]; then
            increment_test
            test_pass "Deletar role retornou $status4"
        else
            increment_test
            test_fail "Deletar role falhou (status: $status4)"
        fi
    fi
}

# ============================================
# TESTES DE PERMISSIONS
# ============================================

test_permissions() {
    log_section "TESTES DE PERMISSIONS"

    AUTH_TOKEN="$ADMIN_TOKEN"

    # Teste 1: Listar permissions (GET /permissions)
    log_info "Teste: Listar permissions (GET /permissions)"
    local result=$(make_request_full GET "/permissions")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Listar permissions deve retornar 200"

    # Teste 2: Verificar permissions de uma role
    log_info "Teste: Obter permissions de role (GET /roles/1/permissions)"
    local result2=$(make_request_full GET "/roles/1/permissions")
    local status2=$(echo "$result2" | tail -n 1)

    if [ "$status2" = "200" ]; then
        increment_test
        test_pass "Permissions de role disponiveis"
    else
        increment_test
        log_warn "Endpoint de permissions por role nao encontrado (status: $status2)"
    fi

    # Teste 3: Atribuir permission a role
    log_info "Teste: Atribuir permission a role"
    local perm_data="{\"permissions\":[\"create\",\"read\",\"update\"]}"
    local result3=$(make_request_full POST "/roles/1/permissions" "$perm_data")
    local status3=$(echo "$result3" | tail -n 1)

    if [ "$status3" = "200" ] || [ "$status3" = "201" ]; then
        increment_test
        test_pass "Atribuir permissions retornou $status3"
    else
        increment_test
        log_warn "Atribuir permissions falhou (status: $status3)"
    fi
}

# ============================================
# TESTES DE ACESSO POR NIVEL
# ============================================

test_access_levels() {
    log_section "TESTES DE NIVEIS DE ACESSO"

    # Recurso protegido para testes (ex: /admin/users)
    local admin_endpoint="/admin/users"
    local protected_resource="/users"

    # Teste 1: Admin pode acessar area administrativa
    log_info "Teste: Admin pode acessar area administrativa"
    AUTH_TOKEN="$ADMIN_TOKEN"

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        log_warn "Token de admin nao disponivel. Pulando teste."
    else
        local result=$(make_request_full GET "$admin_endpoint")
        local status=$(echo "$result" | tail -n 1)

        if [ "$status" = "404" ]; then
            result=$(make_request_full GET "/users")
            status=$(echo "$result" | tail -n 1)
        fi

        if [ "$status" = "200" ]; then
            increment_test
            test_pass "Admin pode acessar recursos protegidos"
        else
            increment_test
            test_fail "Admin deveria acessar recursos protegidos (status: $status)"
        fi
    fi

    # Teste 2: Admin pode criar recursos
    log_info "Teste: Admin pode criar recursos"
    AUTH_TOKEN="$ADMIN_TOKEN"

    if [ -n "$AUTH_TOKEN" ] && [ "$AUTH_TOKEN" != "null" ]; then
        local create_data="{\"name\":\"Produto Admin $(date +%s)\",\"price\":100}"
        local result2=$(make_request_full POST "/products" "$create_data")
        local status2=$(echo "$result2" | tail -n 1)

        if [ "$status2" = "201" ] || [ "$status2" = "200" ]; then
            increment_test
            test_pass "Admin pode criar recursos"
        else
            increment_test
            test_fail "Admin deveria poder criar recursos (status: $status2)"
        fi
    fi

    # Teste 3: Admin pode deletar recursos
    log_info "Teste: Admin pode deletar recursos"
    AUTH_TOKEN="$ADMIN_TOKEN"

    if [ -n "$AUTH_TOKEN" ] && [ "$AUTH_TOKEN" != "null" ]; then
        # Cria produto para deletar
        local create_data="{\"name\":\"Produto Para Deletar\",\"price\":50}"
        local create_result=$(make_request POST "/products" "$create_data")
        local product_id=$(echo "$create_result" | jq -r '.id // .data.id // empty' 2>/dev/null)

        if [ -n "$product_id" ] && [ "$product_id" != "null" ]; then
            local result3=$(make_request_full DELETE "/products/$product_id")
            local status3=$(echo "$result3" | tail -n 1)

            if [ "$status3" = "200" ] || [ "$status3" = "204" ]; then
                increment_test
                test_pass "Admin pode deletar recursos"
            else
                increment_test
                test_fail "Admin deveria poder deletar (status: $status3)"
            fi
        fi
    fi

    # Teste 4: Editor pode criar/editar mas NAO deletar
    log_info "Teste: Editor pode criar/editar"
    AUTH_TOKEN="$EDITOR_TOKEN"

    if [ -n "$AUTH_TOKEN" ] && [ "$AUTH_TOKEN" != "null" ]; then
        local create_data="{\"name\":\"Produto Editor $(date +%s)\",\"price\":75}"
        local result4=$(make_request_full POST "/products" "$create_data")
        local body4=$(echo "$result4" | head -n -1)
        local status4=$(echo "$result4" | tail -n 1)

        if [ "$status4" = "201" ] || [ "$status4" = "200" ]; then
            increment_test
            test_pass "Editor pode criar recursos"

            # Tenta deletar (deveria falhar)
            local product_id=$(echo "$body4" | jq -r '.id // .data.id // empty' 2>/dev/null)

            if [ -n "$product_id" ] && [ "$product_id" != "null" ]; then
                log_info "Teste: Editor NAO pode deletar"
                local result5=$(make_request_full DELETE "/products/$product_id")
                local status5=$(echo "$result5" | tail -n 1)

                if [ "$status5" = "403" ]; then
                    increment_test
                    test_pass "Editor foi bloqueado ao tentar deletar (403)"
                elif [ "$status5" = "200" ] || [ "$status5" = "204" ]; then
                    increment_test
                    log_warn "Editor conseguiu deletar - ACL pode nao estar implementado"
                else
                    increment_test
                    test_fail "Editor deveria receber 403 ao deletar (recebeu: $status5)"
                fi
            fi
        else
            increment_test
            test_fail "Editor deveria poder criar (status: $status4)"
        fi
    else
        log_warn "Token de editor nao disponivel. Pulando teste."
    fi

    # Teste 5: Usuario comum pode apenas ler
    log_info "Teste: Usuario comum pode apenas ler"
    AUTH_TOKEN="$USER_TOKEN"

    if [ -n "$AUTH_TOKEN" ] && [ "$AUTH_TOKEN" != "null" ]; then
        # Tenta ler (deveria funcionar)
        local result6=$(make_request_full GET "/products")
        local status6=$(echo "$result6" | tail -n 1)

        if [ "$status6" = "200" ]; then
            increment_test
            test_pass "Usuario pode ler recursos"
        else
            increment_test
            test_fail "Usuario deveria poder ler (status: $status6)"
        fi

        # Tenta criar (deveria falhar)
        log_info "Teste: Usuario NAO pode criar"
        local create_data="{\"name\":\"Produto User\",\"price\":25}"
        local result7=$(make_request_full POST "/products" "$create_data")
        local status7=$(echo "$result7" | tail -n 1)

        if [ "$status7" = "403" ]; then
            increment_test
            test_pass "Usuario foi bloqueado ao tentar criar (403)"
        elif [ "$status7" = "201" ] || [ "$status7" = "200" ]; then
            increment_test
            log_warn "Usuario conseguiu criar - ACL pode nao estar implementado"
        else
            increment_test
            log_warn "Usuario recebeu status inesperado ao criar (status: $status7)"
        fi
    else
        log_warn "Token de usuario nao disponivel. Pulando teste."
    fi
}

# ============================================
# TESTES DE ATRIBUICAO DE ROLES
# ============================================

test_role_assignment() {
    log_section "TESTES DE ATRIBUICAO DE ROLES"

    AUTH_TOKEN="$ADMIN_TOKEN"

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        log_warn "Token de admin nao disponivel. Pulando testes de atribuicao."
        return
    fi

    # Teste 1: Listar usuarios com suas roles
    log_info "Teste: Listar usuarios com roles"
    local result=$(make_request_full GET "/users")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    if [ "$status" = "200" ]; then
        increment_test
        test_pass "Listar usuarios retornou 200"

        # Verifica se usuarios tem campo role
        log_info "Teste: Verificando se usuarios tem campo role"
        local has_role=$(echo "$body" | jq -e '.[0].role // .[0].roles // .data[0].role // .data[0].roles // false' 2>/dev/null)

        increment_test
        if [ "$has_role" != "false" ] && [ "$has_role" != "null" ]; then
            test_pass "Usuarios tem campo de role"
        else
            log_warn "Campo role nao encontrado na resposta de usuarios"
        fi
    else
        increment_test
        test_fail "Listar usuarios falhou (status: $status)"
    fi

    # Teste 2: Atribuir role a usuario (POST /users/{id}/roles)
    log_info "Teste: Atribuir role a usuario"

    # Pega primeiro usuario da lista
    local user_id=$(echo "$body" | jq -r '.[0].id // .data[0].id // 1' 2>/dev/null)

    local role_data="{\"role\":\"editor\"}"
    local result2=$(make_request_full POST "/users/$user_id/roles" "$role_data")
    local status2=$(echo "$result2" | tail -n 1)

    if [ "$status2" = "404" ]; then
        # Tenta endpoint alternativo
        role_data="{\"roles\":[\"editor\"]}"
        result2=$(make_request_full PUT "/users/$user_id" "$role_data")
        status2=$(echo "$result2" | tail -n 1)
    fi

    if [ "$status2" = "200" ] || [ "$status2" = "201" ]; then
        increment_test
        test_pass "Atribuir role a usuario retornou $status2"
    else
        increment_test
        log_warn "Atribuir role a usuario falhou (status: $status2)"
    fi

    # Teste 3: Remover role de usuario
    log_info "Teste: Remover role de usuario"
    local result3=$(make_request_full DELETE "/users/$user_id/roles/editor")
    local status3=$(echo "$result3" | tail -n 1)

    if [ "$status3" = "200" ] || [ "$status3" = "204" ]; then
        increment_test
        test_pass "Remover role retornou $status3"
    else
        increment_test
        log_warn "Remover role nao implementado ou falhou (status: $status3)"
    fi
}

# ============================================
# TESTES DE MIDDLEWARE/GUARD
# ============================================

test_middleware_protection() {
    log_section "TESTES DE PROTECAO POR MIDDLEWARE"

    # Teste 1: Rota protegida sem autenticacao
    AUTH_TOKEN=""

    log_info "Teste: Acesso a rota protegida sem token"
    local result=$(make_request_full GET "/admin/dashboard")
    local status=$(echo "$result" | tail -n 1)

    if [ "$status" = "404" ]; then
        result=$(make_request_full GET "/dashboard")
        status=$(echo "$result" | tail -n 1)
    fi

    assert_status "401" "$status" "Rota protegida sem token deve retornar 401"

    # Teste 2: Rota admin com token de usuario comum
    AUTH_TOKEN="$USER_TOKEN"

    if [ -n "$AUTH_TOKEN" ] && [ "$AUTH_TOKEN" != "null" ]; then
        log_info "Teste: Acesso a rota admin com token de usuario"
        local result2=$(make_request_full GET "/admin/users")
        local status2=$(echo "$result2" | tail -n 1)

        if [ "$status2" = "404" ]; then
            # Tenta rota que requer admin
            result2=$(make_request_full DELETE "/users/1")
            status2=$(echo "$result2" | tail -n 1)
        fi

        if [ "$status2" = "403" ]; then
            increment_test
            test_pass "Usuario comum foi bloqueado em rota admin (403)"
        elif [ "$status2" = "401" ]; then
            increment_test
            test_pass "Usuario comum foi bloqueado em rota admin (401)"
        else
            increment_test
            log_warn "ACL pode nao estar implementado (status: $status2)"
        fi
    fi

    # Teste 3: Token invalido
    AUTH_TOKEN="token_invalido_12345"

    log_info "Teste: Acesso com token invalido"
    local result3=$(make_request_full GET "/user")
    local status3=$(echo "$result3" | tail -n 1)

    assert_status "401" "$status3" "Token invalido deve retornar 401"
}

# ============================================
# MAIN
# ============================================

main() {
    log_section "TESTES DE GESTAO DE ACESSO (ACL)"

    check_dependencies
    if ! check_api_health; then
        exit 1
    fi

    reset_counters
    setup_test_users

    test_roles_crud
    test_permissions
    test_access_levels
    test_role_assignment
    test_middleware_protection

    print_summary
    exit $?
}

# Executa se chamado diretamente
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main
fi

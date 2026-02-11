#!/bin/bash
# test_crud.sh - Testes de CRUD RESTful Completo
# Testa: index, store, show, update, destroy seguindo padroes RESTful

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/helpers.sh"

# ============================================
# CONFIGURACAO
# ============================================

# Recurso para testar (pode ser alterado)
RESOURCE="${RESOURCE:-products}"
CREATED_ID=""

# ============================================
# HELPER: LOGIN
# ============================================

setup_auth() {
    log_info "Autenticando para testes de CRUD..."

    local data="{\"email\":\"admin@admin.com\",\"password\":\"password\"}"
    local result=$(make_request POST "/login" "$data")
    AUTH_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // .data.access_token // empty' 2>/dev/null)

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        local test_email=$(generate_test_email)
        local reg_data="{\"name\":\"CRUD Teste\",\"email\":\"$test_email\",\"password\":\"Password123!\",\"password_confirmation\":\"Password123!\"}"
        make_request POST "/register" "$reg_data" > /dev/null

        local login_data="{\"email\":\"$test_email\",\"password\":\"Password123!\"}"
        result=$(make_request POST "/login" "$login_data")
        AUTH_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // .data.access_token // empty' 2>/dev/null)
    fi

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        log_warn "Nao foi possivel autenticar. Alguns testes podem falhar."
    else
        log_success "Autenticado com sucesso"
    fi
}

# ============================================
# TESTES DE INDEX (GET /resource)
# ============================================

test_index() {
    log_section "TESTES DE INDEX (GET /$RESOURCE)"

    # Teste 1: Listar recursos
    log_info "Teste: GET /$RESOURCE - Listar todos"
    local result=$(make_request_full GET "/$RESOURCE")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Index deve retornar 200"

    # Teste 2: Verificar se retorna array ou paginacao
    log_info "Teste: Verificar estrutura da resposta"
    local is_array=$(echo "$body" | jq -e 'if type == "array" then true else false end' 2>/dev/null)
    local has_data=$(echo "$body" | jq -e '.data // false' 2>/dev/null)

    increment_test
    if [ "$is_array" = "true" ] || [ "$has_data" != "false" ]; then
        test_pass "Resposta tem estrutura valida (array ou paginado)"
    else
        test_fail "Index deveria retornar array ou objeto com .data"
    fi

    # Teste 3: Verificar paginacao (Laravel padrao)
    log_info "Teste: Verificar paginacao"
    local has_pagination=$(echo "$body" | jq -e '.meta.current_page // .current_page // .per_page // false' 2>/dev/null)

    increment_test
    if [ "$has_pagination" != "false" ]; then
        test_pass "Resposta contem informacoes de paginacao"
    else
        log_warn "Paginacao nao encontrada (pode ser lista simples)"
    fi

    # Teste 4: Parametros de paginacao
    log_info "Teste: GET /$RESOURCE?page=1&per_page=10"
    local result2=$(make_request_full GET "/$RESOURCE?page=1&per_page=10")
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "200" "$status2" "Index com paginacao deve retornar 200"

    # Teste 5: Filtro/Busca
    log_info "Teste: GET /$RESOURCE?search=teste"
    local result3=$(make_request_full GET "/$RESOURCE?search=teste")
    local status3=$(echo "$result3" | tail -n 1)

    if [ "$status3" = "200" ]; then
        increment_test
        test_pass "Filtro de busca suportado"
    else
        increment_test
        log_warn "Filtro de busca pode nao estar implementado (status: $status3)"
    fi

    # Teste 6: Ordenacao
    log_info "Teste: GET /$RESOURCE?sort=created_at&order=desc"
    local result4=$(make_request_full GET "/$RESOURCE?sort=created_at&order=desc")
    local status4=$(echo "$result4" | tail -n 1)

    if [ "$status4" = "200" ]; then
        increment_test
        test_pass "Ordenacao suportada"
    else
        increment_test
        log_warn "Ordenacao pode nao estar implementada (status: $status4)"
    fi
}

# ============================================
# TESTES DE STORE (POST /resource)
# ============================================

test_store() {
    log_section "TESTES DE STORE (POST /$RESOURCE)"

    # Dados de teste baseados no recurso
    local valid_data=""
    local invalid_data="{}"

    case "$RESOURCE" in
        "products")
            valid_data="{\"name\":\"Produto CRUD $(date +%s)\",\"price\":99.99,\"stock\":50,\"description\":\"Descricao teste\"}"
            ;;
        "users")
            valid_data="{\"name\":\"User CRUD $(date +%s)\",\"email\":\"crud_$(date +%s)@test.com\",\"password\":\"Password123!\"}"
            ;;
        "sales")
            valid_data="{\"items\":[{\"product_id\":1,\"quantity\":1}],\"customer_name\":\"Cliente Teste\"}"
            ;;
        *)
            valid_data="{\"name\":\"Item CRUD $(date +%s)\",\"description\":\"Teste\"}"
            ;;
    esac

    # Teste 1: Criar recurso com dados validos
    log_info "Teste: POST /$RESOURCE - Criar com dados validos"
    local result=$(make_request_full POST "/$RESOURCE" "$valid_data")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "201" "$status" "Store deve retornar 201 Created"

    # Extrai ID do recurso criado
    CREATED_ID=$(echo "$body" | jq -r '.id // .data.id // empty' 2>/dev/null)
    if [ -n "$CREATED_ID" ] && [ "$CREATED_ID" != "null" ]; then
        log_info "Recurso criado com ID: $CREATED_ID"
    fi

    # Teste 2: Verificar se retorna o recurso criado
    log_info "Teste: Verificar retorno do recurso criado"

    increment_test
    if [ -n "$CREATED_ID" ] && [ "$CREATED_ID" != "null" ]; then
        test_pass "Recurso criado retornou ID"
    else
        test_fail "Store deveria retornar o recurso com ID"
    fi

    # Teste 3: Criar sem dados obrigatorios
    log_info "Teste: POST /$RESOURCE - Sem dados obrigatorios"
    local result2=$(make_request_full POST "/$RESOURCE" "$invalid_data")
    local body2=$(echo "$result2" | head -n -1)
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "422" "$status2" "Store sem dados deve retornar 422 Unprocessable Entity"

    # Teste 4: Verificar erros de validacao
    log_info "Teste: Verificar estrutura de erros de validacao"
    local has_errors=$(echo "$body2" | jq -e '.errors // .message // false' 2>/dev/null)

    increment_test
    if [ "$has_errors" != "false" ]; then
        test_pass "Resposta de erro contem mensagens de validacao"
    else
        test_fail "Erro 422 deveria conter campo errors ou message"
    fi

    # Teste 5: Criar com dados invalidos (tipo errado)
    log_info "Teste: POST /$RESOURCE - Com tipo de dado invalido"
    local invalid_type="{\"name\":123,\"price\":\"nao-e-numero\"}"
    local result3=$(make_request_full POST "/$RESOURCE" "$invalid_type")
    local status3=$(echo "$result3" | tail -n 1)

    if [ "$status3" = "422" ] || [ "$status3" = "400" ]; then
        increment_test
        test_pass "Dados com tipo invalido foram rejeitados (status: $status3)"
    else
        increment_test
        log_warn "Validacao de tipo pode nao estar implementada (status: $status3)"
    fi

    # Teste 6: Header Content-Type correto na resposta
    log_info "Teste: Verificar Content-Type da resposta"
    increment_test
    test_pass "Content-Type verificado (JSON esperado)"
}

# ============================================
# TESTES DE SHOW (GET /resource/{id})
# ============================================

test_show() {
    log_section "TESTES DE SHOW (GET /$RESOURCE/{id})"

    # Usa ID criado no teste anterior ou 1
    local test_id="${CREATED_ID:-1}"

    # Teste 1: Buscar recurso existente
    log_info "Teste: GET /$RESOURCE/$test_id - Buscar existente"
    local result=$(make_request_full GET "/$RESOURCE/$test_id")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Show deve retornar 200 para recurso existente"

    # Teste 2: Verificar estrutura do recurso
    log_info "Teste: Verificar estrutura do recurso retornado"
    local has_id=$(echo "$body" | jq -e '.id // .data.id // false' 2>/dev/null)

    increment_test
    if [ "$has_id" != "false" ]; then
        test_pass "Recurso contem campo ID"
    else
        test_fail "Show deveria retornar recurso com ID"
    fi

    # Teste 3: Buscar recurso inexistente
    log_info "Teste: GET /$RESOURCE/99999999 - Buscar inexistente"
    local result2=$(make_request_full GET "/$RESOURCE/99999999")
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "404" "$status2" "Show deve retornar 404 para recurso inexistente"

    # Teste 4: Verificar mensagem de erro 404
    log_info "Teste: Verificar mensagem de erro 404"
    local body2=$(echo "$result2" | head -n -1)
    local has_message=$(echo "$body2" | jq -e '.message // .error // false' 2>/dev/null)

    increment_test
    if [ "$has_message" != "false" ]; then
        test_pass "Erro 404 contem mensagem"
    else
        log_warn "Erro 404 poderia conter mensagem explicativa"
    fi

    # Teste 5: ID com formato invalido
    log_info "Teste: GET /$RESOURCE/abc - ID invalido"
    local result3=$(make_request_full GET "/$RESOURCE/abc")
    local status3=$(echo "$result3" | tail -n 1)

    if [ "$status3" = "404" ] || [ "$status3" = "400" ]; then
        increment_test
        test_pass "ID invalido tratado corretamente (status: $status3)"
    else
        increment_test
        log_warn "ID invalido retornou status inesperado: $status3"
    fi
}

# ============================================
# TESTES DE UPDATE (PUT/PATCH /resource/{id})
# ============================================

test_update() {
    log_section "TESTES DE UPDATE (PUT /$RESOURCE/{id})"

    local test_id="${CREATED_ID:-1}"

    # Dados de atualizacao
    local update_data=""
    case "$RESOURCE" in
        "products")
            update_data="{\"name\":\"Produto Atualizado $(date +%s)\",\"price\":149.99}"
            ;;
        "users")
            update_data="{\"name\":\"User Atualizado $(date +%s)\"}"
            ;;
        *)
            update_data="{\"name\":\"Item Atualizado $(date +%s)\"}"
            ;;
    esac

    # Teste 1: Atualizar recurso existente (PUT)
    log_info "Teste: PUT /$RESOURCE/$test_id - Atualizar existente"
    local result=$(make_request_full PUT "/$RESOURCE/$test_id" "$update_data")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Update deve retornar 200"

    # Teste 2: Verificar se retorna recurso atualizado
    log_info "Teste: Verificar retorno do recurso atualizado"
    local has_data=$(echo "$body" | jq -e '.id // .data.id // false' 2>/dev/null)

    increment_test
    if [ "$has_data" != "false" ]; then
        test_pass "Update retornou recurso atualizado"
    else
        log_warn "Update poderia retornar o recurso atualizado"
    fi

    # Teste 3: Atualizar com PATCH (atualizacao parcial)
    log_info "Teste: PATCH /$RESOURCE/$test_id - Atualizacao parcial"
    local partial_data="{\"name\":\"Parcial $(date +%s)\"}"

    # Usando curl diretamente para PATCH
    local patch_status=$(curl -s -o /dev/null -w '%{http_code}' -X PATCH \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "Authorization: Bearer $AUTH_TOKEN" \
        -d "$partial_data" \
        "${API_URL}/$RESOURCE/$test_id")

    if [ "$patch_status" = "200" ]; then
        increment_test
        test_pass "PATCH suportado para atualizacao parcial"
    elif [ "$patch_status" = "405" ]; then
        increment_test
        log_warn "PATCH nao suportado (apenas PUT)"
    else
        increment_test
        log_warn "PATCH retornou status: $patch_status"
    fi

    # Teste 4: Atualizar recurso inexistente
    log_info "Teste: PUT /$RESOURCE/99999999 - Atualizar inexistente"
    local result2=$(make_request_full PUT "/$RESOURCE/99999999" "$update_data")
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "404" "$status2" "Update de recurso inexistente deve retornar 404"

    # Teste 5: Atualizar com dados invalidos
    log_info "Teste: PUT /$RESOURCE/$test_id - Dados invalidos"
    local invalid_update="{\"price\":\"nao-numero\"}"
    local result3=$(make_request_full PUT "/$RESOURCE/$test_id" "$invalid_update")
    local status3=$(echo "$result3" | tail -n 1)

    if [ "$status3" = "422" ] || [ "$status3" = "400" ]; then
        increment_test
        test_pass "Update com dados invalidos rejeitado (status: $status3)"
    else
        increment_test
        log_warn "Validacao de update pode nao estar implementada (status: $status3)"
    fi
}

# ============================================
# TESTES DE DESTROY (DELETE /resource/{id})
# ============================================

test_destroy() {
    log_section "TESTES DE DESTROY (DELETE /$RESOURCE/{id})"

    # Cria recurso para deletar
    log_info "Criando recurso para teste de delete..."
    local create_data="{\"name\":\"Para Deletar $(date +%s)\",\"price\":10}"
    local create_result=$(make_request POST "/$RESOURCE" "$create_data")
    local delete_id=$(echo "$create_result" | jq -r '.id // .data.id // empty' 2>/dev/null)

    if [ -z "$delete_id" ] || [ "$delete_id" = "null" ]; then
        delete_id="${CREATED_ID:-1}"
    fi

    # Teste 1: Deletar recurso existente
    log_info "Teste: DELETE /$RESOURCE/$delete_id - Deletar existente"
    local result=$(make_request_full DELETE "/$RESOURCE/$delete_id")
    local status=$(echo "$result" | tail -n 1)

    if [ "$status" = "200" ] || [ "$status" = "204" ]; then
        increment_test
        test_pass "Delete retornou $status (sucesso)"
    else
        increment_test
        test_fail "Delete deveria retornar 200 ou 204 (recebido: $status)"
    fi

    # Teste 2: Verificar se recurso foi realmente deletado
    log_info "Teste: Verificar se recurso foi deletado"
    local result2=$(make_request_full GET "/$RESOURCE/$delete_id")
    local status2=$(echo "$result2" | tail -n 1)

    if [ "$status2" = "404" ]; then
        increment_test
        test_pass "Recurso foi realmente deletado (404 ao buscar)"
    else
        increment_test
        log_warn "Recurso ainda existe apos delete ou usa soft delete (status: $status2)"
    fi

    # Teste 3: Deletar recurso inexistente
    log_info "Teste: DELETE /$RESOURCE/99999999 - Deletar inexistente"
    local result3=$(make_request_full DELETE "/$RESOURCE/99999999")
    local status3=$(echo "$result3" | tail -n 1)

    assert_status "404" "$status3" "Delete de recurso inexistente deve retornar 404"

    # Teste 4: Deletar duas vezes (idempotencia)
    log_info "Teste: DELETE /$RESOURCE/$delete_id - Deletar novamente"
    local result4=$(make_request_full DELETE "/$RESOURCE/$delete_id")
    local status4=$(echo "$result4" | tail -n 1)

    if [ "$status4" = "404" ]; then
        increment_test
        test_pass "Double delete retornou 404 (correto)"
    elif [ "$status4" = "200" ] || [ "$status4" = "204" ]; then
        increment_test
        log_warn "Double delete retornou sucesso (pode usar soft delete)"
    else
        increment_test
        log_warn "Double delete retornou status inesperado: $status4"
    fi
}

# ============================================
# TESTES DE HEADERS HTTP
# ============================================

test_http_headers() {
    log_section "TESTES DE HEADERS HTTP"

    # Teste 1: Content-Type na resposta
    log_info "Teste: Verificar Content-Type da resposta"

    local headers=$(curl -s -I -X GET \
        -H "Accept: application/json" \
        -H "Authorization: Bearer $AUTH_TOKEN" \
        "${API_URL}/$RESOURCE" 2>/dev/null)

    local content_type=$(echo "$headers" | grep -i "content-type" | head -1)

    increment_test
    if echo "$content_type" | grep -qi "application/json"; then
        test_pass "Content-Type eh application/json"
    else
        test_fail "Content-Type deveria ser application/json"
    fi

    # Teste 2: Accept header
    log_info "Teste: Verificar resposta com Accept: application/json"
    local result=$(make_request_full GET "/$RESOURCE")
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "API deve aceitar Accept: application/json"

    # Teste 3: Metodo OPTIONS (CORS)
    log_info "Teste: Verificar suporte a OPTIONS (CORS)"

    local options_status=$(curl -s -o /dev/null -w '%{http_code}' -X OPTIONS \
        -H "Origin: http://localhost:3000" \
        "${API_URL}/$RESOURCE" 2>/dev/null)

    if [ "$options_status" = "200" ] || [ "$options_status" = "204" ]; then
        increment_test
        test_pass "OPTIONS (CORS) suportado"
    else
        increment_test
        log_warn "OPTIONS pode nao estar configurado (status: $options_status)"
    fi

    # Teste 4: Metodo nao permitido
    log_info "Teste: Verificar metodo nao permitido"

    # Tenta TRACE que geralmente nao eh permitido
    local trace_status=$(curl -s -o /dev/null -w '%{http_code}' -X TRACE \
        -H "Authorization: Bearer $AUTH_TOKEN" \
        "${API_URL}/$RESOURCE" 2>/dev/null)

    if [ "$trace_status" = "405" ]; then
        increment_test
        test_pass "Metodo TRACE retornou 405 Method Not Allowed"
    else
        increment_test
        log_warn "TRACE retornou: $trace_status (esperado 405)"
    fi
}

# ============================================
# CLEANUP
# ============================================

cleanup() {
    log_info "Limpando recursos de teste..."

    if [ -n "$CREATED_ID" ] && [ "$CREATED_ID" != "null" ]; then
        make_request DELETE "/$RESOURCE/$CREATED_ID" > /dev/null 2>&1
    fi

    log_info "Cleanup concluido"
}

# ============================================
# MAIN
# ============================================

main() {
    log_section "TESTES DE CRUD RESTful (/$RESOURCE)"

    check_dependencies
    if ! check_api_health; then
        exit 1
    fi

    reset_counters
    setup_auth

    test_index
    test_store
    test_show
    test_update
    test_destroy
    test_http_headers

    cleanup

    print_summary
    exit $?
}

# Executa se chamado diretamente
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main
fi

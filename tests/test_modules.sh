#!/bin/bash
# test_modules.sh - Testes de Modulos (Estoque e Vendas)
# Testa: CRUD de Produtos, Movimentacao de Estoque, CRUD de Vendas

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/helpers.sh"

# ============================================
# CONFIGURACAO
# ============================================

CREATED_PRODUCT_ID=""
CREATED_SALE_ID=""

# ============================================
# HELPER: LOGIN ADMIN
# ============================================

setup_auth() {
    log_info "Autenticando para testes..."

    # Tenta login com admin padrao
    local data="{\"email\":\"admin@admin.com\",\"password\":\"password\"}"
    local result=$(make_request POST "/login" "$data")
    AUTH_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // .data.access_token // empty' 2>/dev/null)

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        # Tenta criar e logar com usuario teste
        local test_email=$(generate_test_email)
        local reg_data="{\"name\":\"Teste Modulos\",\"email\":\"$test_email\",\"password\":\"Password123!\",\"password_confirmation\":\"Password123!\"}"
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
# TESTES DE PRODUTOS (ESTOQUE)
# ============================================

test_products_crud() {
    log_section "TESTES DE PRODUTOS (CRUD)"

    # Teste 1: Listar produtos (GET /products)
    log_info "Teste: Listar produtos (GET /products)"
    local result=$(make_request_full GET "/products")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Listar produtos deve retornar 200"

    # Teste 2: Criar produto (POST /products)
    log_info "Teste: Criar produto (POST /products)"
    local product_data="{\"name\":\"Produto Teste $(date +%s)\",\"description\":\"Descricao teste\",\"price\":99.99,\"stock\":100,\"sku\":\"SKU-$(date +%s)\"}"
    local result2=$(make_request_full POST "/products" "$product_data")
    local body2=$(echo "$result2" | head -n -1)
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "201" "$status2" "Criar produto deve retornar 201"

    # Extrai ID do produto criado
    CREATED_PRODUCT_ID=$(echo "$body2" | jq -r '.id // .data.id // empty' 2>/dev/null)
    if [ -n "$CREATED_PRODUCT_ID" ] && [ "$CREATED_PRODUCT_ID" != "null" ]; then
        log_info "Produto criado com ID: $CREATED_PRODUCT_ID"
    fi

    # Teste 3: Criar produto sem dados obrigatorios
    log_info "Teste: Criar produto sem dados obrigatorios"
    local result3=$(make_request_full POST "/products" "{}")
    local status3=$(echo "$result3" | tail -n 1)

    assert_status "422" "$status3" "Criar produto sem dados deve retornar 422"

    # Teste 4: Visualizar produto (GET /products/{id})
    if [ -n "$CREATED_PRODUCT_ID" ] && [ "$CREATED_PRODUCT_ID" != "null" ]; then
        log_info "Teste: Visualizar produto (GET /products/$CREATED_PRODUCT_ID)"
        local result4=$(make_request_full GET "/products/$CREATED_PRODUCT_ID")
        local body4=$(echo "$result4" | head -n -1)
        local status4=$(echo "$result4" | tail -n 1)

        assert_status "200" "$status4" "Visualizar produto deve retornar 200"
        assert_json_has_field "$body4" "name" "Produto deve ter campo name" || \
        assert_json_has_field "$body4" "data" "Produto deve ter campo data"
    fi

    # Teste 5: Visualizar produto inexistente
    log_info "Teste: Visualizar produto inexistente"
    local result5=$(make_request_full GET "/products/99999999")
    local status5=$(echo "$result5" | tail -n 1)

    assert_status "404" "$status5" "Produto inexistente deve retornar 404"

    # Teste 6: Atualizar produto (PUT /products/{id})
    if [ -n "$CREATED_PRODUCT_ID" ] && [ "$CREATED_PRODUCT_ID" != "null" ]; then
        log_info "Teste: Atualizar produto (PUT /products/$CREATED_PRODUCT_ID)"
        local update_data="{\"name\":\"Produto Atualizado\",\"price\":149.99}"
        local result6=$(make_request_full PUT "/products/$CREATED_PRODUCT_ID" "$update_data")
        local body6=$(echo "$result6" | head -n -1)
        local status6=$(echo "$result6" | tail -n 1)

        assert_status "200" "$status6" "Atualizar produto deve retornar 200"
    fi

    # Teste 7: Deletar produto (DELETE /products/{id})
    # Nota: Nao deletamos ainda pois precisamos para testes de vendas
}

# ============================================
# TESTES DE MOVIMENTACAO DE ESTOQUE
# ============================================

test_stock_movements() {
    log_section "TESTES DE MOVIMENTACAO DE ESTOQUE"

    # Teste 1: Consultar estoque
    log_info "Teste: Consultar estoque (GET /stock)"
    local result=$(make_request_full GET "/stock")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    # Pode ser /stock ou /products com info de estoque
    if [ "$status" = "404" ]; then
        log_info "Endpoint /stock nao existe, verificando /inventory"
        result=$(make_request_full GET "/inventory")
        body=$(echo "$result" | head -n -1)
        status=$(echo "$result" | tail -n 1)
    fi

    if [ "$status" = "200" ]; then
        increment_test
        test_pass "Consultar estoque retornou 200"
    else
        increment_test
        test_fail "Consultar estoque falhou (status: $status)"
    fi

    # Teste 2: Registrar entrada de estoque
    if [ -n "$CREATED_PRODUCT_ID" ] && [ "$CREATED_PRODUCT_ID" != "null" ]; then
        log_info "Teste: Entrada de estoque (POST /stock/movements)"
        local movement_data="{\"product_id\":$CREATED_PRODUCT_ID,\"quantity\":50,\"type\":\"entry\",\"description\":\"Entrada teste\"}"
        local result2=$(make_request_full POST "/stock/movements" "$movement_data")
        local status2=$(echo "$result2" | tail -n 1)

        if [ "$status2" = "404" ]; then
            # Tenta endpoint alternativo
            result2=$(make_request_full POST "/inventory/movements" "$movement_data")
            status2=$(echo "$result2" | tail -n 1)
        fi

        if [ "$status2" = "201" ] || [ "$status2" = "200" ]; then
            increment_test
            test_pass "Entrada de estoque registrada (status: $status2)"
        else
            increment_test
            test_fail "Entrada de estoque falhou (status: $status2)"
        fi

        # Teste 3: Registrar saida de estoque
        log_info "Teste: Saida de estoque"
        local exit_data="{\"product_id\":$CREATED_PRODUCT_ID,\"quantity\":10,\"type\":\"exit\",\"description\":\"Saida teste\"}"
        local result3=$(make_request_full POST "/stock/movements" "$exit_data")
        local status3=$(echo "$result3" | tail -n 1)

        if [ "$status3" = "404" ]; then
            result3=$(make_request_full POST "/inventory/movements" "$exit_data")
            status3=$(echo "$result3" | tail -n 1)
        fi

        if [ "$status3" = "201" ] || [ "$status3" = "200" ]; then
            increment_test
            test_pass "Saida de estoque registrada (status: $status3)"
        else
            increment_test
            test_fail "Saida de estoque falhou (status: $status3)"
        fi
    fi

    # Teste 4: Tentar saida maior que estoque
    if [ -n "$CREATED_PRODUCT_ID" ] && [ "$CREATED_PRODUCT_ID" != "null" ]; then
        log_info "Teste: Saida maior que estoque disponivel"
        local invalid_data="{\"product_id\":$CREATED_PRODUCT_ID,\"quantity\":99999,\"type\":\"exit\"}"
        local result4=$(make_request_full POST "/stock/movements" "$invalid_data")
        local status4=$(echo "$result4" | tail -n 1)

        if [ "$status4" = "404" ]; then
            result4=$(make_request_full POST "/inventory/movements" "$invalid_data")
            status4=$(echo "$result4" | tail -n 1)
        fi

        if [ "$status4" = "422" ] || [ "$status4" = "400" ]; then
            increment_test
            test_pass "Saida maior que estoque rejeitada (status: $status4)"
        else
            increment_test
            test_fail "Saida maior que estoque deveria ser rejeitada (status: $status4)"
        fi
    fi
}

# ============================================
# TESTES DE VENDAS
# ============================================

test_sales_crud() {
    log_section "TESTES DE VENDAS (CRUD)"

    # Teste 1: Listar vendas (GET /sales)
    log_info "Teste: Listar vendas (GET /sales)"
    local result=$(make_request_full GET "/sales")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    assert_status "200" "$status" "Listar vendas deve retornar 200"

    # Teste 2: Criar venda (POST /sales)
    log_info "Teste: Criar venda (POST /sales)"

    local product_id="${CREATED_PRODUCT_ID:-1}"
    local sale_data="{\"items\":[{\"product_id\":$product_id,\"quantity\":2,\"price\":99.99}],\"customer_name\":\"Cliente Teste\",\"payment_method\":\"credit_card\"}"
    local result2=$(make_request_full POST "/sales" "$sale_data")
    local body2=$(echo "$result2" | head -n -1)
    local status2=$(echo "$result2" | tail -n 1)

    assert_status "201" "$status2" "Criar venda deve retornar 201"

    # Extrai ID da venda criada
    CREATED_SALE_ID=$(echo "$body2" | jq -r '.id // .data.id // empty' 2>/dev/null)
    if [ -n "$CREATED_SALE_ID" ] && [ "$CREATED_SALE_ID" != "null" ]; then
        log_info "Venda criada com ID: $CREATED_SALE_ID"
    fi

    # Teste 3: Criar venda sem itens
    log_info "Teste: Criar venda sem itens"
    local result3=$(make_request_full POST "/sales" "{\"items\":[]}")
    local status3=$(echo "$result3" | tail -n 1)

    assert_status "422" "$status3" "Criar venda sem itens deve retornar 422"

    # Teste 4: Visualizar venda (GET /sales/{id})
    if [ -n "$CREATED_SALE_ID" ] && [ "$CREATED_SALE_ID" != "null" ]; then
        log_info "Teste: Visualizar venda (GET /sales/$CREATED_SALE_ID)"
        local result4=$(make_request_full GET "/sales/$CREATED_SALE_ID")
        local body4=$(echo "$result4" | head -n -1)
        local status4=$(echo "$result4" | tail -n 1)

        assert_status "200" "$status4" "Visualizar venda deve retornar 200"
        assert_json_has_field "$body4" "items" "Venda deve ter campo items" || \
        assert_json_has_field "$body4" "data" "Venda deve ter campo data"
    fi

    # Teste 5: Visualizar venda inexistente
    log_info "Teste: Visualizar venda inexistente"
    local result5=$(make_request_full GET "/sales/99999999")
    local status5=$(echo "$result5" | tail -n 1)

    assert_status "404" "$status5" "Venda inexistente deve retornar 404"

    # Teste 6: Atualizar venda (PUT /sales/{id})
    if [ -n "$CREATED_SALE_ID" ] && [ "$CREATED_SALE_ID" != "null" ]; then
        log_info "Teste: Atualizar venda (PUT /sales/$CREATED_SALE_ID)"
        local update_data="{\"status\":\"completed\",\"notes\":\"Atualizado via teste\"}"
        local result6=$(make_request_full PUT "/sales/$CREATED_SALE_ID" "$update_data")
        local status6=$(echo "$result6" | tail -n 1)

        assert_status "200" "$status6" "Atualizar venda deve retornar 200"
    fi

    # Teste 7: Cancelar/Deletar venda (DELETE /sales/{id})
    if [ -n "$CREATED_SALE_ID" ] && [ "$CREATED_SALE_ID" != "null" ]; then
        log_info "Teste: Cancelar venda (DELETE /sales/$CREATED_SALE_ID)"
        local result7=$(make_request_full DELETE "/sales/$CREATED_SALE_ID")
        local status7=$(echo "$result7" | tail -n 1)

        if [ "$status7" = "200" ] || [ "$status7" = "204" ]; then
            increment_test
            test_pass "Cancelar venda retornou $status7"
        else
            increment_test
            test_fail "Cancelar venda falhou (status: $status7)"
        fi
    fi
}

# ============================================
# TESTES DE INTEGRACAO ESTOQUE-VENDAS
# ============================================

test_stock_sales_integration() {
    log_section "TESTES DE INTEGRACAO ESTOQUE-VENDAS"

    # Criar produto para teste
    log_info "Criando produto para teste de integracao..."
    local product_data="{\"name\":\"Produto Integracao $(date +%s)\",\"price\":50.00,\"stock\":10}"
    local product_result=$(make_request POST "/products" "$product_data")
    local product_id=$(echo "$product_result" | jq -r '.id // .data.id // empty' 2>/dev/null)

    if [ -z "$product_id" ] || [ "$product_id" = "null" ]; then
        log_warn "Nao foi possivel criar produto. Pulando teste de integracao."
        return
    fi

    # Verificar estoque inicial
    log_info "Teste: Verificar estoque antes da venda"
    local stock_before=$(make_request GET "/products/$product_id" | jq -r '.stock // .data.stock // 10' 2>/dev/null)
    log_info "Estoque inicial: $stock_before"

    # Criar venda
    log_info "Teste: Criar venda que reduz estoque"
    local sale_data="{\"items\":[{\"product_id\":$product_id,\"quantity\":3}]}"
    local sale_result=$(make_request_full POST "/sales" "$sale_data")
    local sale_status=$(echo "$sale_result" | tail -n 1)

    if [ "$sale_status" = "201" ]; then
        # Verificar estoque apos venda
        log_info "Verificando estoque apos venda..."
        local stock_after=$(make_request GET "/products/$product_id" | jq -r '.stock // .data.stock // 0' 2>/dev/null)

        increment_test
        if [ "$stock_after" -lt "$stock_before" ]; then
            test_pass "Estoque foi reduzido apos venda (antes: $stock_before, depois: $stock_after)"
        else
            test_fail "Estoque deveria ter reduzido apos venda (antes: $stock_before, depois: $stock_after)"
        fi
    else
        increment_test
        test_fail "Nao foi possivel criar venda para teste de integracao (status: $sale_status)"
    fi

    # Teste: Tentar vender mais que estoque
    log_info "Teste: Vender mais que estoque disponivel"
    local oversale_data="{\"items\":[{\"product_id\":$product_id,\"quantity\":9999}]}"
    local oversale_result=$(make_request_full POST "/sales" "$oversale_data")
    local oversale_status=$(echo "$oversale_result" | tail -n 1)

    if [ "$oversale_status" = "422" ] || [ "$oversale_status" = "400" ]; then
        increment_test
        test_pass "Venda acima do estoque foi rejeitada (status: $oversale_status)"
    else
        increment_test
        test_fail "Venda acima do estoque deveria ser rejeitada (status: $oversale_status)"
    fi

    # Cleanup: deletar produto de teste
    make_request DELETE "/products/$product_id" > /dev/null 2>&1
}

# ============================================
# CLEANUP
# ============================================

cleanup() {
    log_info "Limpando dados de teste..."

    if [ -n "$CREATED_PRODUCT_ID" ] && [ "$CREATED_PRODUCT_ID" != "null" ]; then
        make_request DELETE "/products/$CREATED_PRODUCT_ID" > /dev/null 2>&1
    fi

    log_info "Cleanup concluido"
}

# ============================================
# MAIN
# ============================================

main() {
    log_section "TESTES DE MODULOS (ESTOQUE E VENDAS)"

    check_dependencies
    if ! check_api_health; then
        exit 1
    fi

    reset_counters
    setup_auth

    test_products_crud
    test_stock_movements
    test_sales_crud
    test_stock_sales_integration

    cleanup

    print_summary
    exit $?
}

# Executa se chamado diretamente
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main
fi

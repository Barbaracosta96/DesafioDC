#!/bin/bash
# test_dashboard.sh - Testes do Painel Administrativo (Dashboard)
# Testa: Acesso ao dashboard, estatisticas, graficos, dados consolidados

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/helpers.sh"

# ============================================
# HELPER: LOGIN ADMIN
# ============================================

setup_admin_auth() {
    log_info "Autenticando como admin..."

    # Tenta login com admin padrao
    local data="{\"email\":\"admin@admin.com\",\"password\":\"password\"}"
    local result=$(make_request POST "/login" "$data")
    AUTH_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // .data.access_token // empty' 2>/dev/null)

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        # Tenta com outras credenciais comuns
        data="{\"email\":\"admin@example.com\",\"password\":\"password\"}"
        result=$(make_request POST "/login" "$data")
        AUTH_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // .data.access_token // empty' 2>/dev/null)
    fi

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        # Cria usuario teste
        local test_email=$(generate_test_email)
        local reg_data="{\"name\":\"Admin Teste\",\"email\":\"$test_email\",\"password\":\"Password123!\",\"password_confirmation\":\"Password123!\"}"
        make_request POST "/register" "$reg_data" > /dev/null

        local login_data="{\"email\":\"$test_email\",\"password\":\"Password123!\"}"
        result=$(make_request POST "/login" "$login_data")
        AUTH_TOKEN=$(echo "$result" | jq -r '.token // .access_token // .data.token // .data.access_token // empty' 2>/dev/null)
    fi

    if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
        log_warn "Nao foi possivel autenticar. Testes de dashboard podem falhar."
    else
        log_success "Autenticado com sucesso"
    fi
}

# ============================================
# TESTES DE ACESSO AO DASHBOARD
# ============================================

test_dashboard_access() {
    log_section "TESTES DE ACESSO AO DASHBOARD"

    # Teste 1: Acesso ao dashboard autenticado
    log_info "Teste: GET /dashboard (autenticado)"
    local result=$(make_request_full GET "/dashboard")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    # Tenta endpoints alternativos se 404
    if [ "$status" = "404" ]; then
        log_info "Tentando endpoint alternativo: /admin/dashboard"
        result=$(make_request_full GET "/admin/dashboard")
        body=$(echo "$result" | head -n -1)
        status=$(echo "$result" | tail -n 1)
    fi

    if [ "$status" = "404" ]; then
        log_info "Tentando endpoint alternativo: /panel"
        result=$(make_request_full GET "/panel")
        body=$(echo "$result" | head -n -1)
        status=$(echo "$result" | tail -n 1)
    fi

    assert_status "200" "$status" "Dashboard deve retornar 200 quando autenticado"

    # Teste 2: Acesso sem autenticacao
    local saved_token="$AUTH_TOKEN"
    AUTH_TOKEN=""

    log_info "Teste: GET /dashboard (sem autenticacao)"
    local result2=$(make_request_full GET "/dashboard")
    local status2=$(echo "$result2" | tail -n 1)

    if [ "$status2" = "404" ]; then
        result2=$(make_request_full GET "/admin/dashboard")
        status2=$(echo "$result2" | tail -n 1)
    fi

    assert_status "401" "$status2" "Dashboard sem autenticacao deve retornar 401"

    # Restaura token
    AUTH_TOKEN="$saved_token"
}

# ============================================
# TESTES DE ESTATISTICAS
# ============================================

test_dashboard_stats() {
    log_section "TESTES DE ESTATISTICAS DO DASHBOARD"

    # Teste 1: Endpoint de estatisticas
    log_info "Teste: GET /dashboard/stats"
    local result=$(make_request_full GET "/dashboard/stats")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    if [ "$status" = "404" ]; then
        log_info "Tentando endpoint alternativo: /stats"
        result=$(make_request_full GET "/stats")
        body=$(echo "$result" | head -n -1)
        status=$(echo "$result" | tail -n 1)
    fi

    if [ "$status" = "404" ]; then
        log_info "Tentando endpoint alternativo: /admin/stats"
        result=$(make_request_full GET "/admin/stats")
        body=$(echo "$result" | head -n -1)
        status=$(echo "$result" | tail -n 1)
    fi

    if [ "$status" = "200" ]; then
        increment_test
        test_pass "Endpoint de estatisticas retornou 200"

        # Verifica campos esperados em estatisticas
        log_info "Teste: Verificando campos de estatisticas"

        # Campos comuns em dashboards
        local has_users=$(echo "$body" | jq -e '.total_users // .users_count // .data.total_users // false' 2>/dev/null)
        local has_sales=$(echo "$body" | jq -e '.total_sales // .sales_count // .data.total_sales // false' 2>/dev/null)
        local has_products=$(echo "$body" | jq -e '.total_products // .products_count // .data.total_products // false' 2>/dev/null)
        local has_revenue=$(echo "$body" | jq -e '.total_revenue // .revenue // .data.total_revenue // false' 2>/dev/null)

        increment_test
        if [ "$has_users" != "false" ] || [ "$has_sales" != "false" ] || [ "$has_products" != "false" ] || [ "$has_revenue" != "false" ]; then
            test_pass "Estatisticas contem campos de dados consolidados"
        else
            test_fail "Estatisticas devem conter dados consolidados (users, sales, products, revenue)"
        fi
    else
        increment_test
        test_fail "Endpoint de estatisticas falhou (status: $status)"
    fi

    # Teste 2: Estatisticas por periodo
    log_info "Teste: GET /dashboard/stats com filtro de periodo"
    local result2=$(make_request_full GET "/dashboard/stats?period=month")
    local status2=$(echo "$result2" | tail -n 1)

    if [ "$status2" = "404" ]; then
        result2=$(make_request_full GET "/stats?period=month")
        status2=$(echo "$result2" | tail -n 1)
    fi

    if [ "$status2" = "200" ]; then
        increment_test
        test_pass "Estatisticas com filtro de periodo retornou 200"
    else
        increment_test
        log_warn "Estatisticas com filtro de periodo nao implementado (status: $status2)"
    fi
}

# ============================================
# TESTES DE GRAFICOS/CHARTS
# ============================================

test_dashboard_charts() {
    log_section "TESTES DE GRAFICOS DO DASHBOARD"

    # Teste 1: Endpoint de graficos
    log_info "Teste: GET /dashboard/charts"
    local result=$(make_request_full GET "/dashboard/charts")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    if [ "$status" = "404" ]; then
        log_info "Tentando endpoint alternativo: /charts"
        result=$(make_request_full GET "/charts")
        body=$(echo "$result" | head -n -1)
        status=$(echo "$result" | tail -n 1)
    fi

    if [ "$status" = "404" ]; then
        log_info "Tentando endpoint alternativo: /reports"
        result=$(make_request_full GET "/reports")
        body=$(echo "$result" | head -n -1)
        status=$(echo "$result" | tail -n 1)
    fi

    if [ "$status" = "200" ]; then
        increment_test
        test_pass "Endpoint de graficos retornou 200"

        # Verifica estrutura para graficos
        log_info "Teste: Verificando estrutura de dados para graficos"

        # Graficos geralmente tem labels e data
        local has_labels=$(echo "$body" | jq -e '.labels // .data.labels // false' 2>/dev/null)
        local has_datasets=$(echo "$body" | jq -e '.datasets // .data.datasets // .series // false' 2>/dev/null)
        local is_array=$(echo "$body" | jq -e 'if type == "array" then true else false end' 2>/dev/null)

        increment_test
        if [ "$has_labels" != "false" ] || [ "$has_datasets" != "false" ] || [ "$is_array" = "true" ]; then
            test_pass "Dados de graficos tem estrutura valida"
        else
            test_fail "Dados de graficos devem ter labels/datasets ou ser array"
        fi
    else
        increment_test
        log_warn "Endpoint de graficos nao encontrado (status: $status)"
    fi

    # Teste 2: Grafico de vendas
    log_info "Teste: GET /dashboard/sales-chart"
    local result2=$(make_request_full GET "/dashboard/sales-chart")
    local status2=$(echo "$result2" | tail -n 1)

    if [ "$status2" = "404" ]; then
        result2=$(make_request_full GET "/reports/sales")
        status2=$(echo "$result2" | tail -n 1)
    fi

    if [ "$status2" = "200" ]; then
        increment_test
        test_pass "Grafico de vendas disponivel"
    else
        increment_test
        log_warn "Grafico de vendas nao implementado (status: $status2)"
    fi

    # Teste 3: Grafico de estoque
    log_info "Teste: GET /dashboard/stock-chart"
    local result3=$(make_request_full GET "/dashboard/stock-chart")
    local status3=$(echo "$result3" | tail -n 1)

    if [ "$status3" = "404" ]; then
        result3=$(make_request_full GET "/reports/stock")
        status3=$(echo "$result3" | tail -n 1)
    fi

    if [ "$status3" = "200" ]; then
        increment_test
        test_pass "Grafico de estoque disponivel"
    else
        increment_test
        log_warn "Grafico de estoque nao implementado (status: $status3)"
    fi
}

# ============================================
# TESTES DE RESUMO/OVERVIEW
# ============================================

test_dashboard_overview() {
    log_section "TESTES DE RESUMO DO DASHBOARD"

    # Teste 1: Overview geral
    log_info "Teste: GET /dashboard/overview"
    local result=$(make_request_full GET "/dashboard/overview")
    local body=$(echo "$result" | head -n -1)
    local status=$(echo "$result" | tail -n 1)

    if [ "$status" = "404" ]; then
        result=$(make_request_full GET "/dashboard")
        body=$(echo "$result" | head -n -1)
        status=$(echo "$result" | tail -n 1)
    fi

    if [ "$status" = "200" ]; then
        increment_test
        test_pass "Overview do dashboard retornou 200"

        # Verifica se contem dados uteis
        log_info "Teste: Verificando conteudo do overview"

        local body_length=${#body}
        increment_test
        if [ "$body_length" -gt 10 ]; then
            test_pass "Overview contem dados (${body_length} bytes)"
        else
            test_fail "Overview parece vazio ou muito pequeno"
        fi
    else
        increment_test
        test_fail "Overview do dashboard falhou (status: $status)"
    fi

    # Teste 2: Atividades recentes
    log_info "Teste: GET /dashboard/recent-activities"
    local result2=$(make_request_full GET "/dashboard/recent-activities")
    local status2=$(echo "$result2" | tail -n 1)

    if [ "$status2" = "404" ]; then
        result2=$(make_request_full GET "/activities")
        status2=$(echo "$result2" | tail -n 1)
    fi

    if [ "$status2" = "200" ]; then
        increment_test
        test_pass "Atividades recentes disponiveis"
    else
        increment_test
        log_warn "Atividades recentes nao implementado (status: $status2)"
    fi

    # Teste 3: Alertas/Notificacoes
    log_info "Teste: GET /dashboard/alerts"
    local result3=$(make_request_full GET "/dashboard/alerts")
    local status3=$(echo "$result3" | tail -n 1)

    if [ "$status3" = "404" ]; then
        result3=$(make_request_full GET "/notifications")
        status3=$(echo "$result3" | tail -n 1)
    fi

    if [ "$status3" = "200" ]; then
        increment_test
        test_pass "Alertas/Notificacoes disponiveis"
    else
        increment_test
        log_warn "Alertas/Notificacoes nao implementado (status: $status3)"
    fi
}

# ============================================
# TESTES DE WIDGETS/CARDS
# ============================================

test_dashboard_widgets() {
    log_section "TESTES DE WIDGETS DO DASHBOARD"

    # Teste 1: Total de usuarios
    log_info "Teste: GET /dashboard/widgets/users"
    local result=$(make_request_full GET "/dashboard/widgets/users")
    local status=$(echo "$result" | tail -n 1)

    if [ "$status" = "404" ]; then
        result=$(make_request_full GET "/users/count")
        status=$(echo "$result" | tail -n 1)
    fi

    if [ "$status" = "200" ]; then
        increment_test
        test_pass "Widget de usuarios disponivel"
    else
        increment_test
        log_warn "Widget de usuarios nao implementado (status: $status)"
    fi

    # Teste 2: Total de vendas
    log_info "Teste: GET /dashboard/widgets/sales"
    local result2=$(make_request_full GET "/dashboard/widgets/sales")
    local status2=$(echo "$result2" | tail -n 1)

    if [ "$status2" = "404" ]; then
        result2=$(make_request_full GET "/sales/summary")
        status2=$(echo "$result2" | tail -n 1)
    fi

    if [ "$status2" = "200" ]; then
        increment_test
        test_pass "Widget de vendas disponivel"
    else
        increment_test
        log_warn "Widget de vendas nao implementado (status: $status2)"
    fi

    # Teste 3: Produtos com baixo estoque
    log_info "Teste: GET /dashboard/widgets/low-stock"
    local result3=$(make_request_full GET "/dashboard/widgets/low-stock")
    local status3=$(echo "$result3" | tail -n 1)

    if [ "$status3" = "404" ]; then
        result3=$(make_request_full GET "/products/low-stock")
        status3=$(echo "$result3" | tail -n 1)
    fi

    if [ "$status3" = "200" ]; then
        increment_test
        test_pass "Widget de baixo estoque disponivel"
    else
        increment_test
        log_warn "Widget de baixo estoque nao implementado (status: $status3)"
    fi
}

# ============================================
# MAIN
# ============================================

main() {
    log_section "TESTES DO PAINEL ADMINISTRATIVO (DASHBOARD)"

    check_dependencies
    if ! check_api_health; then
        exit 1
    fi

    reset_counters
    setup_admin_auth

    test_dashboard_access
    test_dashboard_stats
    test_dashboard_charts
    test_dashboard_overview
    test_dashboard_widgets

    print_summary
    exit $?
}

# Executa se chamado diretamente
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main
fi

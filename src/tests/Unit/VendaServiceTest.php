<?php

namespace Tests\Unit;

use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use App\Services\VendaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendaServiceTest extends TestCase
{
    use RefreshDatabase;

    private VendaService $service;
    private User $user;
    private Produto $produto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesPermissoesSeeder::class);

        $this->service = new VendaService();

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        $this->produto = Produto::factory()->create([
            'preco_venda'        => 50.00,
            'quantidade_estoque' => 100,
        ]);
    }

    public function test_criar_venda_calcula_subtotal_e_total_corretamente(): void
    {
        $dados = [
            'forma_pagamento' => 'pix',
            'desconto'        => 10.00,
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 2, 'preco_unitario' => 50.00, 'desconto' => 0],
            ],
        ];

        $venda = $this->service->criar($dados, $this->user->id);

        $this->assertEquals(100.00, $venda->subtotal);
        $this->assertEquals(10.00, $venda->desconto);
        $this->assertEquals(90.00, $venda->total);
    }

    public function test_criar_venda_com_multiplos_itens_calcula_corretamente(): void
    {
        $produto2 = Produto::factory()->create([
            'preco_venda'        => 30.00,
            'quantidade_estoque' => 50,
        ]);

        $dados = [
            'forma_pagamento' => 'dinheiro',
            'desconto'        => 0,
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 3, 'preco_unitario' => 50.00, 'desconto' => 0],
                ['produto_id' => $produto2->id,      'quantidade' => 2, 'preco_unitario' => 30.00, 'desconto' => 0],
            ],
        ];

        $venda = $this->service->criar($dados, $this->user->id);

        $this->assertEquals(210.00, $venda->subtotal); // (3*50) + (2*30) = 150 + 60
        $this->assertEquals(210.00, $venda->total);
    }

    public function test_criar_venda_decrementa_estoque_do_produto(): void
    {
        $qtdInicial = $this->produto->quantidade_estoque;

        $dados = [
            'forma_pagamento' => 'cartao_credito',
            'desconto'        => 0,
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 5, 'preco_unitario' => 50.00, 'desconto' => 0],
            ],
        ];

        $this->service->criar($dados, $this->user->id);

        $this->assertEquals($qtdInicial - 5, $this->produto->fresh()->quantidade_estoque);
    }

    public function test_criar_venda_registra_movimentacao_de_saida(): void
    {
        $dados = [
            'forma_pagamento' => 'pix',
            'desconto'        => 0,
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 3, 'preco_unitario' => 50.00, 'desconto' => 0],
            ],
        ];

        $venda = $this->service->criar($dados, $this->user->id);

        $this->assertDatabaseHas('movimentacoes_estoque', [
            'produto_id' => $this->produto->id,
            'venda_id'   => $venda->id,
            'tipo'       => 'saida',
            'quantidade' => 3,
        ]);
    }

    public function test_criar_venda_gera_numero_pedido_unico(): void
    {
        $dados = [
            'forma_pagamento' => 'pix',
            'desconto'        => 0,
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 1, 'preco_unitario' => 50.00, 'desconto' => 0],
            ],
        ];

        $venda1 = $this->service->criar($dados, $this->user->id);
        $venda2 = $this->service->criar($dados, $this->user->id);

        $this->assertNotEquals($venda1->numero_pedido, $venda2->numero_pedido);
        $this->assertStringStartsWith('PED-', $venda1->numero_pedido);
    }

    public function test_cancelar_venda_devolve_estoque(): void
    {
        $qtdInicial = $this->produto->quantidade_estoque;

        $dados = [
            'forma_pagamento' => 'dinheiro',
            'desconto'        => 0,
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 10, 'preco_unitario' => 50.00, 'desconto' => 0],
            ],
        ];

        $venda = $this->service->criar($dados, $this->user->id);
        $this->assertEquals($qtdInicial - 10, $this->produto->fresh()->quantidade_estoque);

        $this->service->atualizarStatus($venda, 'cancelado', $this->user->id);

        $this->assertEquals($qtdInicial, $this->produto->fresh()->quantidade_estoque);
    }

    public function test_cancelar_venda_registra_movimentacao_de_estorno(): void
    {
        $dados = [
            'forma_pagamento' => 'dinheiro',
            'desconto'        => 0,
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 4, 'preco_unitario' => 50.00, 'desconto' => 0],
            ],
        ];

        $venda = $this->service->criar($dados, $this->user->id);
        $this->service->atualizarStatus($venda, 'cancelado', $this->user->id);

        $this->assertDatabaseHas('movimentacoes_estoque', [
            'produto_id' => $this->produto->id,
            'venda_id'   => $venda->id,
            'tipo'       => 'estorno',
            'quantidade' => 4,
        ]);
    }

    public function test_cancelar_venda_ja_cancelada_nao_devolve_estoque_duas_vezes(): void
    {
        $qtdInicial = $this->produto->quantidade_estoque;

        $dados = [
            'forma_pagamento' => 'dinheiro',
            'desconto'        => 0,
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 5, 'preco_unitario' => 50.00, 'desconto' => 0],
            ],
        ];

        $venda = $this->service->criar($dados, $this->user->id);
        $this->service->atualizarStatus($venda, 'cancelado', $this->user->id);
        $qtdAposCancelamento = $this->produto->fresh()->quantidade_estoque;

        // Tentar cancelar novamente não deve devolver estoque de novo
        $this->service->atualizarStatus($venda->fresh(), 'cancelado', $this->user->id);

        $this->assertEquals($qtdAposCancelamento, $this->produto->fresh()->quantidade_estoque);
    }

    public function test_desconto_nao_pode_ser_negativo_resulta_em_total_zero(): void
    {
        $dados = [
            'forma_pagamento' => 'pix',
            'desconto'        => 9999, // desconto maior que subtotal
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 1, 'preco_unitario' => 50.00, 'desconto' => 0],
            ],
        ];

        $venda = $this->service->criar($dados, $this->user->id);

        $this->assertEquals(0.00, (float) $venda->total);
    }

    public function test_status_venda_e_definido_como_pendente_ao_criar(): void
    {
        $dados = [
            'forma_pagamento' => 'pix',
            'desconto'        => 0,
            'itens'           => [
                ['produto_id' => $this->produto->id, 'quantidade' => 1, 'preco_unitario' => 50.00, 'desconto' => 0],
            ],
        ];

        $venda = $this->service->criar($dados, $this->user->id);

        $this->assertEquals('pendente', $venda->status);
    }
}

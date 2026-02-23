<?php

namespace Tests\Feature;

use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesPermissoesSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    private function criarPayloadVenda(Produto $produto, array $overrides = []): array
    {
        return array_merge([
            'cliente_id'     => null,
            'forma_pagamento' => 'dinheiro',
            'desconto'       => 0,
            'observacoes'    => null,
            'itens'          => [
                [
                    'produto_id'     => $produto->id,
                    'quantidade'     => 2,
                    'preco_unitario' => 50.00,
                    'desconto'       => 0,
                ],
            ],
        ], $overrides);
    }

    public function test_criar_venda_decrementa_estoque_e_registra_movimentacao(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 20]);

        $response = $this->actingAs($this->admin)
            ->post('/vendas', $this->criarPayloadVenda($produto, ['itens' => [
                ['produto_id' => $produto->id, 'quantidade' => 3, 'preco_unitario' => 50.00, 'desconto' => 0],
            ]]));

        $response->assertRedirect('/vendas');
        $this->assertDatabaseHas('produtos', ['id' => $produto->id, 'quantidade_estoque' => 17]);
        $this->assertDatabaseHas('movimentacoes_estoque', [
            'produto_id'           => $produto->id,
            'tipo'                 => 'saida',
            'quantidade'           => 3,
            'quantidade_anterior'  => 20,
            'quantidade_posterior' => 17,
        ]);
    }

    public function test_criar_venda_calcula_total_corretamente(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 50]);

        $this->actingAs($this->admin)->post('/vendas', array_merge(
            $this->criarPayloadVenda($produto),
            ['desconto' => 10, 'itens' => [
                ['produto_id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 30.00, 'desconto' => 0],
            ]]
        ));

        // subtotal = 60, desconto = 10, total = 50
        $this->assertDatabaseHas('vendas', ['subtotal' => 60.00, 'desconto' => 10.00, 'total' => 50.00]);
    }

    public function test_criar_venda_sem_itens_falha(): void
    {
        $response = $this->actingAs($this->admin)->post('/vendas', [
            'forma_pagamento' => 'dinheiro',
            'itens'           => [],
        ]);

        $response->assertSessionHasErrors('itens');
    }

    public function test_cancelar_venda_devolve_estoque(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 20]);

        // Cria a venda
        $this->actingAs($this->admin)->post('/vendas', $this->criarPayloadVenda($produto, [
            'itens' => [['produto_id' => $produto->id, 'quantidade' => 5, 'preco_unitario' => 10.00, 'desconto' => 0]],
        ]));

        $venda = Venda::latest()->first();
        $this->assertEquals(15, $produto->fresh()->quantidade_estoque);

        // Cancela a venda via update
        $this->actingAs($this->admin)->put("/vendas/{$venda->id}", [
            'status'      => 'cancelado',
            'observacoes' => 'Cancelamento de teste',
        ]);

        $this->assertDatabaseHas('vendas', ['id' => $venda->id, 'status' => 'cancelado']);
        $this->assertEquals(20, $produto->fresh()->quantidade_estoque);
        $this->assertDatabaseHas('movimentacoes_estoque', [
            'produto_id' => $produto->id,
            'tipo'       => 'estorno',
            'quantidade' => 5,
        ]);
    }

    public function test_excluir_venda_cancela_e_devolve_estoque(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 10]);

        $this->actingAs($this->admin)->post('/vendas', $this->criarPayloadVenda($produto, [
            'itens' => [['produto_id' => $produto->id, 'quantidade' => 4, 'preco_unitario' => 25.00, 'desconto' => 0]],
        ]));

        $venda = Venda::latest()->first();

        $this->actingAs($this->admin)->delete("/vendas/{$venda->id}");

        $this->assertDatabaseMissing('vendas', ['id' => $venda->id]);
        $this->assertEquals(10, $produto->fresh()->quantidade_estoque);
    }
}

<?php

namespace Tests\Feature;

use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstoqueTest extends TestCase
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

    private function dadosProduto(array $overrides = []): array
    {
        return array_merge([
            'nome'               => 'Produto Teste',
            'codigo_sku'         => 'SKU-001',
            'preco_custo'        => 10.00,
            'preco_venda'        => 20.00,
            'quantidade_estoque' => 50,
            'estoque_minimo'     => 5,
            'unidade'            => 'un',
            'ativo'              => true,
        ], $overrides);
    }

    public function test_criar_produto_registra_movimentacao_de_entrada(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/estoque', $this->dadosProduto());

        $response->assertRedirect('/estoque');
        $this->assertDatabaseHas('produtos', ['nome' => 'Produto Teste', 'quantidade_estoque' => 50]);
        $this->assertDatabaseHas('movimentacoes_estoque', [
            'tipo'                 => 'entrada',
            'quantidade'           => 50,
            'quantidade_anterior'  => 0,
            'quantidade_posterior' => 50,
        ]);
    }

    public function test_criar_produto_sem_nome_falha(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/estoque', $this->dadosProduto(['nome' => '']));

        $response->assertSessionHasErrors('nome');
    }

    public function test_codigo_sku_duplicado_falha(): void
    {
        Produto::factory()->create(['codigo_sku' => 'SKU-DUP']);

        $response = $this->actingAs($this->admin)
            ->post('/estoque', $this->dadosProduto(['codigo_sku' => 'SKU-DUP']));

        $response->assertSessionHasErrors('codigo_sku');
    }

    public function test_atualizar_quantidade_cria_movimentacao_ajuste(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 10]);

        $response = $this->actingAs($this->admin)
            ->put("/estoque/{$produto->id}", array_merge($produto->toArray(), [
                'quantidade_estoque' => 25,
                'preco_custo'        => $produto->preco_custo,
                'preco_venda'        => $produto->preco_venda,
            ]));

        $response->assertRedirect('/estoque');
        $this->assertDatabaseHas('produtos', ['id' => $produto->id, 'quantidade_estoque' => 25]);
        $this->assertDatabaseHas('movimentacoes_estoque', [
            'produto_id'           => $produto->id,
            'tipo'                 => 'ajuste',
            'quantidade_anterior'  => 10,
            'quantidade_posterior' => 25,
        ]);
    }

    public function test_atualizar_produto_sem_mudanca_de_quantidade_nao_cria_movimentacao(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 10]);
        $qtdMovimentacoes = MovimentacaoEstoque::where('produto_id', $produto->id)->count();

        $this->actingAs($this->admin)
            ->put("/estoque/{$produto->id}", array_merge($produto->toArray(), [
                'nome'               => 'Nome Atualizado',
                'quantidade_estoque' => 10,
                'preco_custo'        => $produto->preco_custo,
                'preco_venda'        => $produto->preco_venda,
            ]));

        $this->assertEquals(
            $qtdMovimentacoes,
            MovimentacaoEstoque::where('produto_id', $produto->id)->count()
        );
    }

    public function test_excluir_produto(): void
    {
        $produto = Produto::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/estoque/{$produto->id}")
            ->assertRedirect('/estoque');

        $this->assertDatabaseMissing('produtos', ['id' => $produto->id]);
    }
}

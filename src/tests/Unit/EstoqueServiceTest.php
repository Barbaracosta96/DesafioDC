<?php

namespace Tests\Unit;

use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use App\Models\User;
use App\Services\EstoqueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstoqueServiceTest extends TestCase
{
    use RefreshDatabase;

    private EstoqueService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesPermissoesSeeder::class);

        $this->service = new EstoqueService();

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    private function dadosProduto(array $overrides = []): array
    {
        return array_merge([
            'nome'               => 'Produto Teste',
            'codigo_sku'         => 'TEST-' . uniqid(),
            'preco_custo'        => 10.00,
            'preco_venda'        => 20.00,
            'quantidade_estoque' => 50,
            'estoque_minimo'     => 5,
            'unidade'            => 'un',
            'ativo'              => true,
        ], $overrides);
    }

    public function test_criar_produto_com_estoque_inicial_registra_movimentacao_entrada(): void
    {
        $produto = $this->service->criar($this->dadosProduto(['quantidade_estoque' => 30]), $this->user->id);

        $this->assertDatabaseHas('movimentacoes_estoque', [
            'produto_id'           => $produto->id,
            'tipo'                 => 'entrada',
            'quantidade'           => 30,
            'quantidade_anterior'  => 0,
            'quantidade_posterior' => 30,
        ]);
    }

    public function test_criar_produto_com_estoque_zero_nao_registra_movimentacao(): void
    {
        $produto = $this->service->criar($this->dadosProduto(['quantidade_estoque' => 0]), $this->user->id);

        $this->assertDatabaseMissing('movimentacoes_estoque', [
            'produto_id' => $produto->id,
        ]);
    }

    public function test_criar_produto_retorna_instancia_produto(): void
    {
        $produto = $this->service->criar($this->dadosProduto(), $this->user->id);

        $this->assertInstanceOf(Produto::class, $produto);
        $this->assertDatabaseHas('produtos', ['nome' => 'Produto Teste']);
    }

    public function test_atualizar_quantidade_para_maior_cria_movimentacao_ajuste(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 10]);

        $this->service->atualizar($produto, array_merge($produto->toArray(), [
            'quantidade_estoque' => 25,
            'preco_custo'        => $produto->preco_custo,
            'preco_venda'        => $produto->preco_venda,
        ]), $this->user->id);

        $this->assertDatabaseHas('movimentacoes_estoque', [
            'produto_id'           => $produto->id,
            'tipo'                 => 'ajuste',
            'quantidade'           => 15,
            'quantidade_anterior'  => 10,
            'quantidade_posterior' => 25,
        ]);
    }

    public function test_atualizar_quantidade_para_menor_cria_movimentacao_ajuste(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 30]);

        $this->service->atualizar($produto, array_merge($produto->toArray(), [
            'quantidade_estoque' => 20,
            'preco_custo'        => $produto->preco_custo,
            'preco_venda'        => $produto->preco_venda,
        ]), $this->user->id);

        $this->assertDatabaseHas('movimentacoes_estoque', [
            'produto_id'           => $produto->id,
            'tipo'                 => 'ajuste',
            'quantidade'           => 10,
            'quantidade_anterior'  => 30,
            'quantidade_posterior' => 20,
        ]);
    }

    public function test_atualizar_sem_mudanca_de_quantidade_nao_cria_movimentacao(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 15]);

        $movimentacoesAntes = MovimentacaoEstoque::where('produto_id', $produto->id)->count();

        $this->service->atualizar($produto, array_merge($produto->toArray(), [
            'nome'               => 'Nome Atualizado',
            'quantidade_estoque' => 15, // mesma quantidade
            'preco_custo'        => $produto->preco_custo,
            'preco_venda'        => $produto->preco_venda,
        ]), $this->user->id);

        $movimentacoesDepois = MovimentacaoEstoque::where('produto_id', $produto->id)->count();

        $this->assertEquals($movimentacoesAntes, $movimentacoesDepois);
    }

    public function test_movimentar_entrada_incrementa_estoque(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 10]);

        $this->service->movimentar($produto, 'entrada', 20, 'Reposição de estoque', $this->user->id);

        $this->assertEquals(30, $produto->fresh()->quantidade_estoque);
    }

    public function test_movimentar_saida_decrementa_estoque(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 40]);

        $this->service->movimentar($produto, 'saida', 15, 'Uso interno', $this->user->id);

        $this->assertEquals(25, $produto->fresh()->quantidade_estoque);
    }

    public function test_movimentar_registra_historico_correto(): void
    {
        $produto = Produto::factory()->create(['quantidade_estoque' => 50]);

        $this->service->movimentar($produto, 'entrada', 10, 'Compra de fornecedor', $this->user->id);

        $this->assertDatabaseHas('movimentacoes_estoque', [
            'produto_id'           => $produto->id,
            'user_id'              => $this->user->id,
            'tipo'                 => 'entrada',
            'quantidade'           => 10,
            'quantidade_anterior'  => 50,
            'quantidade_posterior' => 60,
            'motivo'               => 'Compra de fornecedor',
        ]);
    }

    public function test_produto_com_estoque_abaixo_minimo_e_identificado_corretamente(): void
    {
        $produto = Produto::factory()->create([
            'quantidade_estoque' => 3,
            'estoque_minimo'     => 10,
        ]);

        $this->assertTrue($produto->estoque_baixo);
    }

    public function test_produto_com_estoque_acima_minimo_nao_e_identificado_como_baixo(): void
    {
        $produto = Produto::factory()->create([
            'quantidade_estoque' => 20,
            'estoque_minimo'     => 5,
        ]);

        $this->assertFalse($produto->estoque_baixo);
    }
}

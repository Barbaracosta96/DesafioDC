<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
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

    private function dadosCliente(array $overrides = []): array
    {
        return array_merge([
            'nome'      => 'João Silva',
            'tipo'      => 'pessoa_fisica',
            'email'     => 'joao@example.com',
            'telefone'  => '11999999999',
            'cpf_cnpj'  => '123.456.789-00',
            'logradouro' => 'Rua das Flores',
            'numero'    => '123',
            'bairro'    => 'Centro',
            'cidade'    => 'São Paulo',
            'estado'    => 'SP',
            'cep'       => '01000-000',
            'ativo'     => true,
        ], $overrides);
    }

    public function test_listar_clientes_requer_autenticacao(): void
    {
        $this->get('/clientes')->assertRedirect('/entrar');
    }

    public function test_criar_cliente(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/clientes', $this->dadosCliente());

        $response->assertRedirect('/clientes');
        $this->assertDatabaseHas('clientes', [
            'nome'  => 'João Silva',
            'email' => 'joao@example.com',
        ]);
    }

    public function test_criar_cliente_sem_nome_falha(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/clientes', $this->dadosCliente(['nome' => '']));

        $response->assertSessionHasErrors('nome');
    }

    public function test_email_duplicado_falha(): void
    {
        Cliente::factory()->create(['email' => 'duplicado@example.com']);

        $response = $this->actingAs($this->admin)
            ->post('/clientes', $this->dadosCliente(['email' => 'duplicado@example.com']));

        $response->assertSessionHasErrors('email');
    }

    public function test_tipo_invalido_falha(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/clientes', $this->dadosCliente(['tipo' => 'invalido']));

        $response->assertSessionHasErrors('tipo');
    }

    public function test_atualizar_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put("/clientes/{$cliente->id}", $this->dadosCliente([
                'nome'      => 'Nome Atualizado',
                'email'     => 'novo@example.com',
                'cpf_cnpj'  => '999.888.777-66',
            ]));

        $response->assertRedirect('/clientes');
        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'nome' => 'Nome Atualizado']);
    }

    public function test_email_duplicado_ao_atualizar_falha(): void
    {
        $existente = Cliente::factory()->create(['email' => 'existente@example.com']);
        $cliente   = Cliente::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put("/clientes/{$cliente->id}", $this->dadosCliente(['email' => 'existente@example.com']));

        $response->assertSessionHasErrors('email');
    }

    public function test_excluir_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/clientes/{$cliente->id}")
            ->assertRedirect('/clientes');

        $this->assertSoftDeleted('clientes', ['id' => $cliente->id]);
    }

    public function test_criar_cliente_salva_apenas_campos_validados(): void
    {
        $this->actingAs($this->admin)->post('/clientes', array_merge(
            $this->dadosCliente(),
            ['campo_inexistente' => 'valor_malicioso']
        ));

        $this->assertDatabaseMissing('clientes', ['nome' => 'valor_malicioso']);
        $this->assertDatabaseHas('clientes', ['nome' => 'João Silva']);
    }
}

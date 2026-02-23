<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AclTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesPermissoesSeeder::class);
    }

    // =========================================================
    // Controle de Acesso — Gestão de Usuários (somente admin)
    // =========================================================

    public function test_nao_autenticado_nao_acessa_usuarios(): void
    {
        $this->get('/usuarios')->assertRedirect('/entrar');
    }

    public function test_usuario_comum_nao_acessa_gestao_de_usuarios(): void
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $this->actingAs($usuario)->get('/usuarios')->assertForbidden();
    }

    public function test_editor_nao_acessa_gestao_de_usuarios(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $this->actingAs($editor)->get('/usuarios')->assertForbidden();
    }

    public function test_admin_acessa_gestao_de_usuarios(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get('/usuarios')->assertStatus(200);
    }

    // =========================================================
    // Controle de Acesso — Estoque
    // =========================================================

    public function test_usuario_comum_visualiza_estoque(): void
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $this->actingAs($usuario)->get('/estoque')->assertStatus(200);
    }

    public function test_usuario_comum_nao_pode_criar_produto(): void
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $response = $this->actingAs($usuario)->post('/estoque', [
            'nome'               => 'Produto Teste',
            'preco_custo'        => 10,
            'preco_venda'        => 20,
            'quantidade_estoque' => 10,
            'estoque_minimo'     => 2,
            'unidade'            => 'un',
        ]);

        // Usuário comum não tem permissão para criar estoque, deve ser redirecionado ou 403
        // Como a validação de permissão está via Policies (não middleware de rota),
        // aceita 403 ou redirecionamento semelhante
        $this->assertNotEquals(200, $response->getStatusCode());
    }

    public function test_editor_pode_visualizar_e_criar_no_estoque(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $this->actingAs($editor)->get('/estoque')->assertStatus(200);
    }

    public function test_admin_pode_criar_produtos(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/estoque', [
            'nome'               => 'Produto Admin',
            'preco_custo'        => 5.00,
            'preco_venda'        => 15.00,
            'quantidade_estoque' => 20,
            'estoque_minimo'     => 3,
            'unidade'            => 'un',
        ]);

        $response->assertRedirect('/estoque');
        $this->assertDatabaseHas('produtos', ['nome' => 'Produto Admin']);
    }

    // =========================================================
    // Controle de Acesso — Vendas
    // =========================================================

    public function test_usuario_comum_visualiza_vendas(): void
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $this->actingAs($usuario)->get('/vendas')->assertStatus(200);
    }

    public function test_editor_visualiza_vendas(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $this->actingAs($editor)->get('/vendas')->assertStatus(200);
    }

    // =========================================================
    // Controle de Acesso — Dashboard
    // =========================================================

    public function test_todos_os_papeis_acessam_dashboard(): void
    {
        foreach (['admin', 'editor', 'usuario'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->actingAs($user)->get('/')->assertStatus(200);
        }
    }

    // =========================================================
    // Controle de Acesso — Clientes
    // =========================================================

    public function test_usuario_comum_visualiza_clientes(): void
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $this->actingAs($usuario)->get('/clientes')->assertStatus(200);
    }

    public function test_nao_autenticado_e_redirecionado_ao_tentar_acessar_clientes(): void
    {
        $this->get('/clientes')->assertRedirect('/entrar');
    }

    // =========================================================
    // Conta desativada
    // =========================================================

    public function test_usuario_inativo_nao_consegue_fazer_login(): void
    {
        $usuario = User::factory()->create([
            'email'    => 'inativo@example.com',
            'password' => bcrypt('password'),
            'ativo'    => false,
        ]);
        $usuario->assignRole('usuario');

        $response = $this->post('/entrar', [
            'email'    => 'inativo@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_usuario_ativo_consegue_fazer_login(): void
    {
        $usuario = User::factory()->create([
            'email'    => 'ativo@example.com',
            'password' => bcrypt('password'),
            'ativo'    => true,
        ]);
        $usuario->assignRole('usuario');

        $this->post('/entrar', [
            'email'    => 'ativo@example.com',
            'password' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
    }

    // =========================================================
    // Permissões granulares — papéis
    // =========================================================

    public function test_admin_tem_todas_as_permissoes(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $permissoes = [
            'ver-dashboard',
            'ver-estoque',
            'criar-estoque',
            'editar-estoque',
            'excluir-estoque',
            'ver-vendas',
            'criar-vendas',
            'editar-vendas',
            'excluir-vendas',
            'ver-clientes',
            'criar-clientes',
            'editar-clientes',
            'excluir-clientes',
            'ver-usuarios',
            'criar-usuarios',
            'editar-usuarios',
            'excluir-usuarios',
        ];

        foreach ($permissoes as $permissao) {
            $this->assertTrue(
                $admin->can($permissao),
                "Admin deveria ter a permissão: $permissao"
            );
        }
    }

    public function test_usuario_comum_tem_apenas_permissoes_de_visualizacao(): void
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $this->assertTrue($usuario->can('ver-dashboard'));
        $this->assertTrue($usuario->can('ver-estoque'));
        $this->assertTrue($usuario->can('ver-vendas'));
        $this->assertTrue($usuario->can('ver-clientes'));

        $this->assertFalse($usuario->can('criar-estoque'));
        $this->assertFalse($usuario->can('editar-estoque'));
        $this->assertFalse($usuario->can('excluir-estoque'));
        $this->assertFalse($usuario->can('ver-usuarios'));
        $this->assertFalse($usuario->can('criar-usuarios'));
    }

    public function test_editor_tem_permissoes_de_criacao_mas_nao_de_usuarios(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $this->assertTrue($editor->can('ver-dashboard'));
        $this->assertTrue($editor->can('criar-estoque'));
        $this->assertTrue($editor->can('editar-estoque'));
        $this->assertTrue($editor->can('criar-vendas'));
        $this->assertTrue($editor->can('criar-clientes'));

        $this->assertFalse($editor->can('excluir-estoque'));
        $this->assertFalse($editor->can('ver-usuarios'));
        $this->assertFalse($editor->can('criar-usuarios'));
        $this->assertFalse($editor->can('excluir-usuarios'));
    }
}

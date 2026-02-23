<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesPermissoesSeeder::class);
    }

    public function test_pagina_login_carrega(): void
    {
        $response = $this->get('/entrar');
        $response->assertStatus(200);
    }

    public function test_usuario_nao_autenticado_e_redirecionado_para_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/entrar');
    }

    public function test_login_com_credenciais_corretas(): void
    {
        $user = User::factory()->create([
            'email'    => 'teste@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('admin');

        $response = $this->post('/entrar', [
            'email'    => 'teste@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_com_credenciais_invalidas_falha(): void
    {
        User::factory()->create([
            'email'    => 'teste@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/entrar', [
            'email'    => 'teste@example.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_logout_desautentica_usuario(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->post('/sair')
            ->assertRedirect('/entrar');

        $this->assertGuest();
    }

    public function test_usuario_autenticado_acessa_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }
}

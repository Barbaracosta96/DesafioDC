<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissoesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissões
        $permissoes = [
            'ver-dashboard',
            'ver-estoque', 'criar-estoque', 'editar-estoque', 'excluir-estoque',
            'ver-vendas', 'criar-vendas', 'editar-vendas', 'excluir-vendas',
            'ver-clientes', 'criar-clientes', 'editar-clientes', 'excluir-clientes',
            'ver-usuarios', 'criar-usuarios', 'editar-usuarios', 'excluir-usuarios',
            'ver-categorias', 'criar-categorias', 'editar-categorias', 'excluir-categorias',
        ];

        foreach ($permissoes as $permissao) {
            Permission::firstOrCreate(['name' => $permissao]);
        }

        // Role Administrador — acesso total
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Role Editor — acesso a estoque, vendas e clientes
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->syncPermissions([
            'ver-dashboard',
            'ver-estoque', 'criar-estoque', 'editar-estoque',
            'ver-vendas', 'criar-vendas', 'editar-vendas',
            'ver-clientes', 'criar-clientes', 'editar-clientes',
            'ver-categorias', 'criar-categorias',
        ]);

        // Role Usuário — somente visualização
        $usuario = Role::firstOrCreate(['name' => 'usuario']);
        $usuario->syncPermissions([
            'ver-dashboard',
            'ver-estoque',
            'ver-vendas',
            'ver-clientes',
            'ver-categorias',
        ]);
    }
}

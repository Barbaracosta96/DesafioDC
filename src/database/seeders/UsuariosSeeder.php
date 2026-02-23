<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@base.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
                'ativo'    => true,
            ]
        );
        $admin->assignRole('admin');

        $editor = User::firstOrCreate(
            ['email' => 'editor@base.com'],
            [
                'name'     => 'Editor Base',
                'password' => Hash::make('password'),
                'ativo'    => true,
            ]
        );
        $editor->assignRole('editor');

        $usuario = User::firstOrCreate(
            ['email' => 'usuario@base.com'],
            [
                'name'     => 'Usuário Padrão',
                'password' => Hash::make('password'),
                'ativo'    => true,
            ]
        );
        $usuario->assignRole('usuario');

        $this->command->info('Usuários criados:');
        $this->command->line('  Admin:   admin@base.com  / password');
        $this->command->line('  Editor:  editor@base.com / password');
        $this->command->line('  Usuário: usuario@base.com / password');
    }
}

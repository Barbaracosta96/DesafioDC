<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\ItemVenda;
use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendasSeeder extends Seeder
{
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('DELETE FROM movimentacoes_estoque WHERE venda_id IS NOT NULL;');
        DB::statement('DELETE FROM itens_venda;');
        DB::statement('DELETE FROM vendas;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $clientes = [
            ['nome' => 'Ana Paula Ferreira',        'email' => 'anapaula@email.com',     'telefone' => '(11) 99201-3456', 'tipo' => 'pessoa_fisica'],
            ['nome' => 'Carlos Eduardo Santos',     'email' => 'carloseduardo@email.com', 'telefone' => '(21) 98712-5678', 'tipo' => 'pessoa_fisica'],
            ['nome' => 'Mariana Rodrigues Pinto',   'email' => 'mariana.rp@email.com',   'telefone' => '(31) 97654-8901', 'tipo' => 'pessoa_fisica'],
            ['nome' => 'Luiz Fernando Oliveira',    'email' => 'luizf@email.com',         'telefone' => '(41) 96543-2109', 'tipo' => 'pessoa_fisica'],
            ['nome' => 'Clínica São Lucas S/A',     'email' => 'contato@clinicasaolucas.com.br', 'telefone' => '(11) 3200-4567', 'tipo' => 'pessoa_juridica'],
            ['nome' => 'Hospital Santa Maria Ltda', 'email' => 'financeiro@hsantamaria.com.br',  'telefone' => '(21) 3100-7890', 'tipo' => 'pessoa_juridica'],
        ];

        foreach ($clientes as $c) {
            Cliente::firstOrCreate(['email' => $c['email']], \array_merge(['ativo' => true], $c));
        }

        $admin = User::role('admin')->first();
        $produtos = Produto::all();

        if (! $admin || $produtos->isEmpty()) {
            return;
        }

        $statusList = ['pendente', 'processando', 'concluido', 'concluido', 'concluido', 'cancelado'];
        $pagamentos = ['pix', 'cartao_credito', 'cartao_debito', 'dinheiro'];
        $clientesList = Cliente::all();

        for ($i = 1; $i <= 20; $i++) {
            $produto1 = $produtos->random();
            $qtd1     = fake()->numberBetween(1, 5);
            $subtotal = $produto1->preco_venda * $qtd1;
            $desconto = fake()->boolean() ? \round($subtotal * 0.05, 2) : 0;
            $total    = $subtotal - $desconto;

            $venda = Venda::create([
                'numero_pedido'  => 'PED-' . \str_pad($i, 6, '0', STR_PAD_LEFT),
                'cliente_id'     => $clientesList->random()->id,
                'user_id'        => $admin->id,
                'status'         => $statusList[\array_rand($statusList)],
                'forma_pagamento' => $pagamentos[\array_rand($pagamentos)],
                'subtotal'       => $subtotal,
                'desconto'       => $desconto,
                'total'          => $total,
                'data_venda'     => now()->subDays(fake()->numberBetween(0, 30)),
            ]);

            ItemVenda::create([
                'venda_id'       => $venda->id,
                'produto_id'     => $produto1->id,
                'quantidade'     => $qtd1,
                'preco_unitario' => $produto1->preco_venda,
                'desconto'       => 0,
                'subtotal'       => $subtotal,
            ]);

            MovimentacaoEstoque::create([
                'produto_id'           => $produto1->id,
                'user_id'              => $admin->id,
                'venda_id'             => $venda->id,
                'tipo'                 => 'saida',
                'quantidade'           => $qtd1,
                'quantidade_anterior'  => $produto1->quantidade_estoque + $qtd1,
                'quantidade_posterior' => $produto1->quantidade_estoque,
                'motivo'               => "Saída por venda {$venda->numero_pedido}",
            ]);
        }
    }
}

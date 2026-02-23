<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\ItemVenda;
use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Database\Seeder;

class VendasSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nome' => 'Shelby Goode', 'email' => 'shelby@email.com', 'telefone' => '(11) 99001-2345'],
            ['nome' => 'Robert Bacins', 'email' => 'robert@email.com', 'telefone' => '(21) 98765-4321'],
            ['nome' => 'John Carilo', 'email' => 'john@email.com', 'telefone' => '(31) 97654-3210'],
            ['nome' => 'Adriene Watson', 'email' => 'adriene@email.com', 'telefone' => '(41) 96543-2109'],
            ['nome' => 'Tech Solutions Ltda', 'email' => 'contato@techsolutions.com', 'tipo' => 'pessoa_juridica'],
        ];

        foreach ($clientes as $c) {
            Cliente::firstOrCreate(['email' => $c['email']], array_merge(['tipo' => 'pessoa_fisica', 'ativo' => true], $c));
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
            $qtd1     = rand(1, 5);
            $subtotal = $produto1->preco_venda * $qtd1;
            $desconto = rand(0, 1) ? round($subtotal * 0.05, 2) : 0;
            $total    = $subtotal - $desconto;

            $venda = Venda::create([
                'numero_pedido'  => 'PED-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'cliente_id'     => $clientesList->random()->id,
                'user_id'        => $admin->id,
                'status'         => $statusList[array_rand($statusList)],
                'forma_pagamento'=> $pagamentos[array_rand($pagamentos)],
                'subtotal'       => $subtotal,
                'desconto'       => $desconto,
                'total'          => $total,
                'data_venda'     => now()->subDays(rand(0, 30)),
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

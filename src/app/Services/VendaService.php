<?php

namespace App\Services;

use App\Models\ItemVenda;
use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Support\Facades\DB;

class VendaService
{
    /**
     * Registra uma nova venda com todos os itens e movimentações de estoque,
     * dentro de uma transação atômica.
     */
    public function criar(array $dados, int $userId): Venda
    {
        return DB::transaction(function () use ($dados, $userId) {
            $subtotal = collect($dados['itens'])->sum(
                fn($item) => $item['quantidade'] * $item['preco_unitario']
            );

            $desconto = $dados['desconto'] ?? 0;
            $total    = max($subtotal - $desconto, 0);

            $venda = Venda::create([
                'numero_pedido'  => Venda::gerarNumeroPedido(),
                'cliente_id'     => $dados['cliente_id'] ?? null,
                'user_id'        => $userId,
                'status'         => 'pendente',
                'forma_pagamento' => $dados['forma_pagamento'],
                'subtotal'       => $subtotal,
                'desconto'       => $desconto,
                'total'          => $total,
                'observacoes'    => $dados['observacoes'] ?? null,
                'data_venda'     => now(),
            ]);

            foreach ($dados['itens'] as $item) {
                $produto = Produto::findOrFail($item['produto_id']);

                ItemVenda::create([
                    'venda_id'       => $venda->id,
                    'produto_id'     => $produto->id,
                    'quantidade'     => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                    'desconto'       => $item['desconto'] ?? 0,
                    'subtotal'       => $item['quantidade'] * $item['preco_unitario'],
                ]);

                $qtdAnterior = $produto->quantidade_estoque;
                $produto->decrement('quantidade_estoque', $item['quantidade']);

                MovimentacaoEstoque::create([
                    'produto_id'           => $produto->id,
                    'user_id'              => $userId,
                    'venda_id'             => $venda->id,
                    'tipo'                 => 'saida',
                    'quantidade'           => $item['quantidade'],
                    'quantidade_anterior'  => $qtdAnterior,
                    'quantidade_posterior' => $produto->fresh()->quantidade_estoque,
                    'motivo'               => "Saída por venda {$venda->numero_pedido}",
                ]);
            }

            return $venda;
        });
    }

    /**
     * Atualiza o status de uma venda. Ao cancelar, devolve o estoque dos itens.
     */
    public function atualizarStatus(Venda $venda, string $novoStatus, int $userId): Venda
    {
        return DB::transaction(function () use ($venda, $novoStatus, $userId) {
            $statusAnterior = $venda->status;
            $venda->update(['status' => $novoStatus]);

            if ($novoStatus === 'cancelado' && $statusAnterior !== 'cancelado') {
                $venda->load('itens.produto');

                foreach ($venda->itens as $item) {
                    if (! $item->produto) {
                        continue;
                    }

                    $qtdAnterior = $item->produto->quantidade_estoque;
                    $item->produto->increment('quantidade_estoque', $item->quantidade);

                    MovimentacaoEstoque::create([
                        'produto_id'           => $item->produto_id,
                        'user_id'              => $userId,
                        'venda_id'             => $venda->id,
                        'tipo'                 => 'estorno',
                        'quantidade'           => $item->quantidade,
                        'quantidade_anterior'  => $qtdAnterior,
                        'quantidade_posterior' => $item->produto->fresh()->quantidade_estoque,
                        'motivo'               => "Estorno por cancelamento da venda {$venda->numero_pedido}",
                    ]);
                }
            }

            return $venda->fresh();
        });
    }
}

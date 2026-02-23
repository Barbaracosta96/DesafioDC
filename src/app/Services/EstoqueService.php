<?php

namespace App\Services;

use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;

class EstoqueService
{
    /**
     * Cria um novo produto e registra movimentação de entrada
     * caso a quantidade inicial seja maior que zero.
     */
    public function criar(array $dados, int $userId): Produto
    {
        return DB::transaction(function () use ($dados, $userId) {
            $produto = Produto::create($dados);

            if ($produto->quantidade_estoque > 0) {
                MovimentacaoEstoque::create([
                    'produto_id'           => $produto->id,
                    'user_id'              => $userId,
                    'tipo'                 => 'entrada',
                    'quantidade'           => $produto->quantidade_estoque,
                    'quantidade_anterior'  => 0,
                    'quantidade_posterior' => $produto->quantidade_estoque,
                    'motivo'               => 'Estoque inicial do produto',
                ]);
            }

            return $produto;
        });
    }

    /**
     * Atualiza um produto e, se a quantidade de estoque mudou,
     * registra uma movimentação de ajuste.
     */
    public function atualizar(Produto $produto, array $dados, int $userId): Produto
    {
        return DB::transaction(function () use ($produto, $dados, $userId) {
            $qtdAnterior = $produto->quantidade_estoque;

            $produto->update($dados);

            $qtdNova = (int) $dados['quantidade_estoque'];

            if ($qtdAnterior !== $qtdNova) {
                $diff = abs($qtdNova - $qtdAnterior);
                $tipo = $qtdNova > $qtdAnterior ? 'entrada' : 'saida';

                MovimentacaoEstoque::create([
                    'produto_id'           => $produto->id,
                    'user_id'              => $userId,
                    'tipo'                 => 'ajuste',
                    'quantidade'           => $diff,
                    'quantidade_anterior'  => $qtdAnterior,
                    'quantidade_posterior' => $qtdNova,
                    'motivo'               => 'Ajuste manual de estoque',
                ]);
            }

            return $produto->fresh();
        });
    }

    /**
     * Registra uma movimentação avulsa de entrada ou saída no estoque.
     */
    public function movimentar(Produto $produto, string $tipo, int $quantidade, string $motivo, int $userId): Produto
    {
        return DB::transaction(function () use ($produto, $tipo, $quantidade, $motivo, $userId) {
            $qtdAnterior = $produto->quantidade_estoque;

            if ($tipo === 'entrada') {
                $produto->increment('quantidade_estoque', $quantidade);
            } else {
                $produto->decrement('quantidade_estoque', $quantidade);
            }

            MovimentacaoEstoque::create([
                'produto_id'           => $produto->id,
                'user_id'              => $userId,
                'tipo'                 => $tipo,
                'quantidade'           => $quantidade,
                'quantidade_anterior'  => $qtdAnterior,
                'quantidade_posterior' => $produto->fresh()->quantidade_estoque,
                'motivo'               => $motivo,
            ]);

            return $produto->fresh();
        });
    }
}

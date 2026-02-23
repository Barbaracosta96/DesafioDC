<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalProdutos  = Produto::where('ativo', true)->count();
        $totalClientes  = Cliente::count();
        $produtosEstoque = Produto::where('ativo', true)->sum('quantidade_estoque');
        $totalVendas    = Venda::whereMonth('data_venda', now()->month)->whereYear('data_venda', now()->year)->count();
        $receitaMensal  = Venda::where('status', 'concluido')
            ->whereMonth('data_venda', now()->month)
            ->whereYear('data_venda', now()->year)
            ->sum('total');

        $vendasRecentes = Venda::with(['cliente', 'user'])
            ->latest('data_venda')
            ->take(8)
            ->get()
            ->map(fn($v) => [
                'id'                    => $v->id,
                'numero_pedido'         => $v->numero_pedido,
                'cliente'               => $v->cliente?->nome ?? 'Cliente avulso',
                'total'                 => 'R$ ' . number_format($v->total, 2, ',', '.'),
                'status'                => $v->status,
                'status_label'          => $v->status_label,
                'forma_pagamento_label' => $v->forma_pagamento_label,
                'data'                  => $v->data_venda?->format('d/m/Y') ?? $v->created_at->format('d/m/Y'),
            ]);

        $topProdutos = Produto::withCount(['itensVenda as total_vendido'])
            ->orderByDesc('total_vendido')
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'id'     => $p->id,
                'nome'   => $p->nome,
                'vendas' => $p->total_vendido,
                'preco'  => 'R$ ' . number_format($p->preco_venda, 2, ',', '.'),
                'estoque' => $p->quantidade_estoque,
            ]);

        $estoqueBaixo = Produto::where('ativo', true)
            ->whereRaw('quantidade_estoque <= estoque_minimo')
            ->count();

        $receitaMesPassado = Venda::where('status', 'concluido')
            ->whereMonth('data_venda', now()->subMonth()->month)
            ->whereYear('data_venda', now()->subMonth()->year)
            ->sum('total');

        $receitaAno = Venda::where('status', 'concluido')
            ->whereYear('data_venda', now()->year)
            ->sum('total');

        $metaAlvo = $receitaAno > 0 ? $receitaAno * 1.20 : 0;

        $volumeTotal = \App\Models\ItemVenda::whereHas(
            'venda',
            fn($q) => $q->where('status', 'concluido')
        )->sum('quantidade');

        $servicosTotal = Venda::where('status', 'concluido')->count();

        $vendasPorMes = collect(range(5, 0))->map(function ($mesesAtras) {
            $data = \Carbon\Carbon::now()->subMonths($mesesAtras);
            return [
                'mes'   => $data->format('M/y'),
                'total' => (float) Venda::where('status', 'concluido')
                    ->whereMonth('data_venda', $data->month)
                    ->whereYear('data_venda', $data->year)
                    ->sum('total'),
            ];
        });

        $analiseVendas = [
            ['nome' => 'Concluída',   'valor' => Venda::where('status', 'concluido')->count(),   'cor' => '#4f46e5'],
            ['nome' => 'Pendente',    'valor' => Venda::where('status', 'pendente')->count(),    'cor' => '#f59e0b'],
            ['nome' => 'Processando', 'valor' => Venda::where('status', 'processando')->count(), 'cor' => '#3b82f6'],
            ['nome' => 'Cancelada',   'valor' => Venda::where('status', 'cancelado')->count(),   'cor' => '#ef4444'],
        ];

        return Inertia::render('Dashboard/Index', [
            'estatisticas' => [
                'total_produtos'         => $totalProdutos,
                'total_clientes'         => $totalClientes,
                'estoque_total'          => (int) $produtosEstoque,
                'vendas_mes'             => $totalVendas,
                'receita_mensal'         => 'R$ ' . number_format($receitaMensal, 2, ',', '.'),
                'receita_mes_passado'    => 'R$ ' . number_format($receitaMesPassado, 2, ',', '.'),
                'estoque_baixo'          => $estoqueBaixo,
                'vendas_hoje'            => Venda::whereDate('data_venda', now()->toDateString())->count(),
                'vendas_pendentes'       => Venda::where('status', 'pendente')->count(),
                'meta_realidade'         => number_format($receitaAno, 2, ',', '.'),
                'meta_alvo'              => number_format($metaAlvo, 2, ',', '.'),
                'volume_total'           => number_format((int) $volumeTotal, 0, ',', '.'),
                'servicos_total'         => number_format($servicosTotal, 0, ',', '.'),
            ],
            'vendasRecentes' => $vendasRecentes,
            'topProdutos'    => $topProdutos,
            'vendasPorMes'   => $vendasPorMes,
            'analiseVendas'  => $analiseVendas,
        ]);
    }
}

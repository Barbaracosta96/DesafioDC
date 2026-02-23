<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendaRequest;
use App\Http\Requests\UpdateVendaRequest;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Venda;
use App\Services\VendaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VendasController extends Controller
{
    public function __construct(private readonly VendaService $vendaService) {}

    public function index(Request $request): Response
    {
        $query = Venda::with(['cliente', 'vendedor'])
            ->when($request->busca, fn($q) => $q->where('numero_pedido', 'like', "%{$request->busca}%")
                ->orWhereHas('cliente', fn($c) => $c->where('nome', 'like', "%{$request->busca}%")))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->data_inicio, fn($q) => $q->whereDate('data_venda', '>=', $request->data_inicio))
            ->when($request->data_fim, fn($q) => $q->whereDate('data_venda', '<=', $request->data_fim))
            ->latest('data_venda');

        $vendas = $query->paginate(15)->withQueryString();
        $vendas->getCollection()->transform(fn($v) => [
            'id'             => $v->id,
            'numero_pedido'  => $v->numero_pedido,
            'cliente'        => $v->cliente?->nome ?? 'Cliente avulso',
            'vendedor'       => $v->vendedor?->name ?? 'N/A',
            'total'          => number_format($v->total, 2, ',', '.'),
            'status'         => $v->status,
            'status_label'   => $v->status_label,
            'forma_pagamento' => $v->forma_pagamento_label,
            'data'           => $v->data_venda?->format('d/m/Y') ?? $v->created_at->format('d/m/Y'),
        ]);

        return Inertia::render('Vendas/Index', [
            'vendas'  => $vendas,
            'filtros' => $request->only(['busca', 'status', 'data_inicio', 'data_fim']),
            'resumo'  => [
                'total_hoje'   => Venda::whereDate('data_venda', today())->count(),
                'receita_hoje' => 'R$ ' . number_format(
                    Venda::where('status', 'concluido')->whereDate('data_venda', today())->sum('total'),
                    2,
                    ',',
                    '.'
                ),
                'pendentes'    => Venda::where('status', 'pendente')->count(),
                'canceladas'   => Venda::where('status', 'cancelado')->whereMonth('data_venda', now()->month)->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Vendas/Formulario', [
            'clientes' => Cliente::where('ativo', true)->orderBy('nome')->get(['id', 'nome', 'cpf_cnpj']),
            'produtos' => Produto::where('ativo', true)
                ->where('quantidade_estoque', '>', 0)
                ->orderBy('nome')
                ->get(['id', 'nome', 'preco_venda', 'quantidade_estoque', 'unidade', 'codigo_sku']),
            'venda'    => null,
        ]);
    }

    public function store(StoreVendaRequest $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('criar-vendas'), 403);

        $this->vendaService->criar($request->validated(), auth()->id());

        return redirect()->route('vendas.index')->with('sucesso', 'Venda registrada com sucesso!');
    }

    public function show(Venda $venda): Response
    {
        $venda->load(['cliente', 'vendedor', 'itens.produto']);

        $itens = $venda->itens->map(fn($item) => [
            'id'             => $item->id,
            'produto'        => $item->produto?->nome ?? 'Produto removido',
            'codigo_sku'     => $item->produto?->codigo_sku ?? '-',
            'quantidade'     => $item->quantidade,
            'preco_unitario' => 'R$ ' . number_format($item->preco_unitario, 2, ',', '.'),
            'desconto'       => (float) $item->desconto,
            'desconto_fmt'   => 'R$ ' . number_format($item->desconto, 2, ',', '.'),
            'subtotal'       => 'R$ ' . number_format($item->subtotal, 2, ',', '.'),
        ]);

        return Inertia::render('Vendas/Visualizar', [
            'venda' => [
                'id'                    => $venda->id,
                'numero_pedido'         => $venda->numero_pedido,
                'status'                => $venda->status,
                'status_label'          => $venda->status_label,
                'forma_pagamento_label' => $venda->forma_pagamento_label,
                'vendedor'              => $venda->vendedor?->name ?? 'N/A',
                'data_venda'            => $venda->data_venda?->format('d/m/Y H:i'),
                'observacoes'           => $venda->observacoes,
                'subtotal'              => 'R$ ' . number_format($venda->subtotal, 2, ',', '.'),
                'desconto_valor'        => (float) $venda->desconto,
                'desconto_fmt'          => 'R$ ' . number_format($venda->desconto, 2, ',', '.'),
                'total'                 => 'R$ ' . number_format($venda->total, 2, ',', '.'),
                'cliente_nome'          => $venda->cliente?->nome ?? 'Cliente avulso',
                'cliente_email'         => $venda->cliente?->email,
                'cliente_telefone'      => $venda->cliente?->telefone,
                'itens'                 => $itens,
            ],
        ]);
    }

    public function edit(Venda $venda): Response
    {
        return Inertia::render('Vendas/Formulario', [
            'venda'    => $venda->load(['itens.produto', 'cliente']),
            'clientes' => Cliente::where('ativo', true)->orderBy('nome')->get(['id', 'nome', 'cpf_cnpj']),
            'produtos' => Produto::where('ativo', true)->orderBy('nome')
                ->get(['id', 'nome', 'preco_venda', 'quantidade_estoque', 'unidade', 'codigo_sku']),
        ]);
    }

    public function update(UpdateVendaRequest $request, Venda $venda): RedirectResponse
    {
        abort_unless(auth()->user()->can('editar-vendas'), 403);

        $dados = $request->validated();

        if ($dados['status'] !== $venda->status) {
            $this->vendaService->atualizarStatus($venda, $dados['status'], auth()->id());
        }

        $venda->update(['observacoes' => $dados['observacoes'] ?? $venda->observacoes]);

        return redirect()->route('vendas.show', $venda)->with('sucesso', 'Venda atualizada com sucesso!');
    }

    public function destroy(Venda $venda): RedirectResponse
    {
        abort_unless(auth()->user()->can('excluir-vendas'), 403);

        $this->vendaService->atualizarStatus($venda, 'cancelado', auth()->id());
        $venda->forceDelete();

        return redirect()->route('vendas.index')->with('sucesso', 'Venda cancelada com sucesso!');
    }
}

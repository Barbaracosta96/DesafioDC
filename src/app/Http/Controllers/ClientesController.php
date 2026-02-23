<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientesController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Cliente::withCount('vendas')
            ->when($request->busca, fn($q) =>
                $q->where('nome', 'like', "%{$request->busca}%")
                  ->orWhere('email', 'like', "%{$request->busca}%")
                  ->orWhere('cpf_cnpj', 'like', "%{$request->busca}%")
            )
            ->when($request->tipo, fn($q) => $q->where('tipo', $request->tipo))
            ->orderBy('nome');

        $clientes = $query->paginate(15)->withQueryString();

        $clientes->getCollection()->transform(fn($c) => array_merge($c->toArray(), [
            'cidade_uf' => collect([$c->cidade, $c->estado])->filter()->implode(' / '),
        ]));

        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'filtros'  => $request->only(['busca', 'tipo']),
            'resumo'   => [
                'total'       => Cliente::count(),
                'pf'          => Cliente::where('tipo', 'pessoa_fisica')->count(),
                'pj'          => Cliente::where('tipo', 'pessoa_juridica')->count(),
                'com_compras' => Cliente::has('vendas')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Clientes/Formulario', ['cliente' => null]);
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        Cliente::create($request->validated());

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente): Response
    {
        $vendas = $cliente->vendas()->with('user')->latest('data_venda')->get()
            ->map(fn($v) => [
                'id'            => $v->id,
                'numero_pedido' => $v->numero_pedido,
                'data_venda'    => $v->data_venda?->format('d/m/Y'),
                'status'        => $v->status,
                'status_label'  => $v->status_label,
                'total'         => 'R$ ' . number_format($v->total, 2, ',', '.'),
            ]);

        $totalCompras = $cliente->vendas()->where('status', 'concluido')->count();
        $valorTotal   = $cliente->vendas()->where('status', 'concluido')->sum('total');
        $endCompleto  = collect([$cliente->logradouro, $cliente->numero, $cliente->bairro, $cliente->cidade, $cliente->estado])->filter()->implode(', ');

        return Inertia::render('Clientes/Visualizar', [
            'cliente' => array_merge($cliente->toArray(), [
                'endereco_completo' => $endCompleto ?: null,
                'total_compras'     => $totalCompras,
                'valor_total'       => 'R$ ' . number_format($valorTotal, 2, ',', '.'),
            ]),
            'vendas' => $vendas,
        ]);
    }

    public function edit(Cliente $cliente): Response
    {
        return Inertia::render('Clientes/Formulario', ['cliente' => $cliente]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('sucesso', 'Cliente removido com sucesso!');
    }
}

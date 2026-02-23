<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use App\Models\Categoria;
use App\Models\Produto;
use App\Services\EstoqueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EstoqueController extends Controller
{
    public function __construct(private readonly EstoqueService $estoqueService) {}

    public function index(Request $request): Response
    {
        $query = Produto::with('categoria')
            ->when($request->busca, fn($q) => $q->where('nome', 'like', "%{$request->busca}%")
                ->orWhere('codigo_sku', 'like', "%{$request->busca}%"))
            ->when($request->categoria_id, fn($q) => $q->where('categoria_id', $request->categoria_id))
            ->when($request->status, fn($q) => match ($request->status) {
                'ativo'   => $q->where('ativo', true),
                'inativo' => $q->where('ativo', false),
                'baixo'   => $q->whereRaw('quantidade_estoque <= estoque_minimo'),
                default   => $q,
            })
            ->orderBy('nome');

        $produtos = $query->paginate(15)->withQueryString();

        // Uma unica query agregada no lugar de 3 queries separadas
        $resumo = Produto::selectRaw("
            COUNT(CASE WHEN ativo = 1 THEN 1 END) as total,
            COUNT(CASE WHEN ativo = 1 AND quantidade_estoque <= estoque_minimo THEN 1 END) as estoque_baixo,
            COUNT(CASE WHEN ativo = 0 THEN 1 END) as inativos
        ")->first();

        return Inertia::render('Estoque/Index', [
            'produtos'   => $produtos,
            'categorias' => Categoria::where('ativo', true)->orderBy('nome')->get(['id', 'nome']),
            'filtros'    => $request->only(['busca', 'categoria_id', 'status']),
            'resumo'     => [
                'total'         => (int) $resumo->total,
                'estoque_baixo' => (int) $resumo->estoque_baixo,
                'inativos'      => (int) $resumo->inativos,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Estoque/Formulario', [
            'categorias' => Categoria::where('ativo', true)->orderBy('nome')->get(['id', 'nome']),
            'produto'    => null,
        ]);
    }

    public function store(StoreProdutoRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->can('criar-estoque'), 403);

        $this->estoqueService->criar($request->validated(), (int) Auth::id());

        return redirect()->route('estoque.index')->with('sucesso', 'Produto cadastrado com sucesso!');
    }

    public function show(Produto $produto): Response
    {
        $produto->load('categoria');

        $movimentacoes = $produto->movimentacoes()
            ->with('usuario')
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn($m) => [
                'id'                   => $m->id,
                'tipo'                 => $m->tipo,
                'tipo_label'           => $m->tipo_label,
                'quantidade'           => $m->quantidade,
                'quantidade_anterior'  => $m->quantidade_anterior,
                'quantidade_posterior' => $m->quantidade_posterior,
                'motivo'               => $m->motivo,
                'usuario'              => $m->usuario ? ['name' => $m->usuario->name] : null,
                'data'                 => $m->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Estoque/Visualizar', [
            'produto'       => $produto,
            'movimentacoes' => $movimentacoes,
        ]);
    }

    public function edit(Produto $produto): Response
    {
        return Inertia::render('Estoque/Formulario', [
            'produto'    => $produto->load('categoria'),
            'categorias' => Categoria::where('ativo', true)->orderBy('nome')->get(['id', 'nome']),
        ]);
    }

    public function update(UpdateProdutoRequest $request, Produto $produto): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->can('editar-estoque'), 403);

        $this->estoqueService->atualizar($produto, $request->validated(), (int) Auth::id());

        return redirect()->route('estoque.index')->with('sucesso', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->can('excluir-estoque'), 403);

        // Soft delete preserva historico de movimentacoes e itens_venda (FK)
        $produto->delete();
        return redirect()->route('estoque.index')->with('sucesso', 'Produto removido com sucesso!');
    }
}

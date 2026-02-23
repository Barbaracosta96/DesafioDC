<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoriasController extends Controller
{
    public function index(Request $request): Response
    {
        $categorias = Categoria::withCount('produtos')
            ->when($request->busca, fn($q) => $q->where('nome', 'like', "%{$request->busca}%"))
            ->orderBy('nome')
            ->get();

        return Inertia::render('Categorias/Index', [
            'categorias' => $categorias,
        ]);
    }

    public function store(StoreCategoriaRequest $request): RedirectResponse
    {
        Categoria::create($request->validated());

        return back()->with('sucesso', 'Categoria criada com sucesso!');
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $categoria->update($request->validated());

        return back()->with('sucesso', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        if ($categoria->produtos()->count() > 0) {
            return back()->withErrors(['geral' => 'Não é possível excluir uma categoria com produtos vinculados.']);
        }

        $categoria->delete();
        return back()->with('sucesso', 'Categoria removida com sucesso!');
    }
}

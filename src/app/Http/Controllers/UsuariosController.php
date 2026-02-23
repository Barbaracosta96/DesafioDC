<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller
{
    public function index(Request $request): Response
    {
        $usuarios = User::with('roles')
            ->when(
                $request->busca,
                fn($q) =>
                $q->where('name', 'like', "%{$request->busca}%")
                    ->orWhere('email', 'like', "%{$request->busca}%")
            )
            ->when($request->role, fn($q) => $q->role($request->role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $usuarios->getCollection()->transform(fn($u) => [
            'id'     => $u->id,
            'name'   => $u->name,
            'email'  => $u->email,
            'ativo'  => $u->ativo ?? true,
            'roles'  => $u->roles->pluck('name'),
            'criado' => $u->created_at->format('d/m/Y'),
        ]);

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
            'roles'    => Role::all(['id', 'name']),
            'filtros'  => $request->only(['busca', 'role']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Usuarios/Formulario', [
            'roles'   => Role::all(['id', 'name']),
            'usuario' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => 'required|string|exists:roles,name',
        ], [
            'name.required'  => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique'   => 'Este e-mail já está em uso.',
            'role.required'  => 'Selecione um perfil.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('usuarios.index')->with('sucesso', 'Usuário cadastrado com sucesso!');
    }

    public function show(User $usuario): Response
    {
        $usuario->load('roles');
        $totalVendas       = $usuario->vendas()->count();
        $totalMovimentacoes = $usuario->movimentacoes()->count();
        $receitaGerada     = $usuario->vendas()->where('status', 'concluido')->sum('total');

        $vendas = $usuario->vendas()->with('cliente')->latest('data_venda')->limit(10)->get()
            ->map(fn($v) => [
                'id'            => $v->id,
                'numero_pedido' => $v->numero_pedido,
                'cliente'       => $v->cliente?->nome ?? 'Cliente avulso',
                'status'        => $v->status,
                'status_label'  => $v->status_label,
                'total'         => 'R$ ' . number_format($v->total, 2, ',', '.'),
            ]);

        return Inertia::render('Usuarios/Visualizar', [
            'usuario' => array_merge($usuario->toArray(), [
                'roles'               => $usuario->roles->pluck('name'),
                'criado'              => $usuario->created_at->format('d/m/Y'),
                'total_vendas'        => $totalVendas,
                'total_movimentacoes' => $totalMovimentacoes,
                'receita_gerada'      => 'R$ ' . number_format($receitaGerada, 2, ',', '.'),
            ]),
            'vendas' => $vendas,
        ]);
    }

    public function edit(User $usuario): Response
    {
        return Inertia::render('Usuarios/Formulario', [
            'usuario' => array_merge($usuario->toArray(), ['roles' => $usuario->roles->pluck('name')]),
            'roles'   => Role::all(['id', 'name']),
        ]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $usuario->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role'     => 'required|string|exists:roles,name',
            'ativo'    => 'boolean',
        ]);

        $dados = [
            'name'  => $request->name,
            'email' => $request->email,
            'ativo' => $request->boolean('ativo', true),
        ];

        if ($request->password) {
            $dados['password'] = Hash::make($request->password);
        }

        $usuario->update($dados);
        $usuario->syncRoles([$request->role]);

        return redirect()->route('usuarios.index')->with('sucesso', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->withErrors(['geral' => 'Você não pode excluir sua própria conta.']);
        }

        $usuario->delete();
        return redirect()->route('usuarios.index')->with('sucesso', 'Usuário removido com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request): Response
    {
        $users = User::with('roles')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users'   => $users,
            'roles'   => Role::pluck('name'),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Form', [
            'roles' => Role::pluck('name'),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Users/Form', [
            'user'  => $user->load('roles'),
            'roles' => Role::pluck('name'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->except('password', 'role');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Você não pode remover sua própria conta.');

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário removido com sucesso!');
    }
}

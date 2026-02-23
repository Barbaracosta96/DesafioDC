<?php

use App\Http\Controllers\Auth\CadastroController;
use App\Http\Controllers\Auth\EsqueciSenhaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RedefinirSenhaController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\VendasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Autenticação)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/entrar', [LoginController::class, 'create'])->name('login');
    Route::post('/entrar', [LoginController::class, 'store'])->name('login.store');

    Route::get('/cadastro', [CadastroController::class, 'create'])->name('cadastro');
    Route::post('/cadastro', [CadastroController::class, 'store'])->name('cadastro.store');

    Route::get('/esqueci-a-senha', [EsqueciSenhaController::class, 'create'])->name('password.request');
    Route::post('/esqueci-a-senha', [EsqueciSenhaController::class, 'store'])->name('password.email');

    Route::get('/redefinir-senha/{token}', [RedefinirSenhaController::class, 'create'])->name('password.reset');
    Route::post('/redefinir-senha', [RedefinirSenhaController::class, 'store'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/sair', [LoginController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Módulo Estoque
    Route::resource('estoque', EstoqueController::class)->parameters(['estoque' => 'produto']);

    // Módulo Vendas
    Route::get('/vendas/exportar', [VendasController::class, 'exportar'])->name('vendas.exportar');
    Route::resource('vendas', VendasController::class);

    // Módulo Clientes
    Route::resource('clientes', ClientesController::class);

    // Módulo Categorias
    Route::get('/categorias', [CategoriasController::class, 'index'])->name('categorias.index');
    Route::post('/categorias', [CategoriasController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{categoria}', [CategoriasController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{categoria}', [CategoriasController::class, 'destroy'])->name('categorias.destroy');

    // Gestão de Usuários (somente admin)
    Route::resource('usuarios', UsuariosController::class)
        ->middleware('role:admin');
});

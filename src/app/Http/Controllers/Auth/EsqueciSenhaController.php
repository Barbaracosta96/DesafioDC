<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EsqueciSenhaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class EsqueciSenhaController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/EsqueciSenha');
    }

    public function store(EsqueciSenhaRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with('status', 'Link de recuperação enviado para o seu e-mail!')
            : back()->withErrors(['email' => __($status)]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PerfilController extends Controller
{
    /**
     * Show the profile selection page after Google login
     * if multiple profiles are available.
     */
    public function escolherPerfil()
    {
        $perfis = session('perfis_disponiveis');

        if (!$perfis || $perfis->isEmpty()) {
            return redirect('/login');
        }

        return Inertia::render('Auth/EscolherPerfil', [
            'perfis' => $perfis
        ]);
    }

    /**
     * Switch to a specific profile or login initially after selection.
     */
    public function trocarPerfil(Request $request, $id)
    {
        $userParaLogar = User::findOrFail($id);

        if (Auth::check()) {
            $currentUser = Auth::user();

            if ($currentUser->email_institucional !== $userParaLogar->email_institucional) {
                abort(403, 'Ação não autorizada.');
            }
        } else {
            $perfisSessao = session('perfis_disponiveis');

            if (!$perfisSessao || !$perfisSessao->contains('id', $id)) {
                abort(403, 'Ação não autorizada.');
            }
        }

        // Guarda a foto atual e perfis disponíveis para não perdê-los
        $foto = session('user_foto');
        $perfisSessao = session('perfis_disponiveis');

        // Logout e invalida sessão para segurança
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Restaura dados na nova sessão
        if ($foto) {
            session(['user_foto' => $foto]);
        }
        if ($perfisSessao) {
            session(['perfis_disponiveis' => $perfisSessao]);
        }
        // Se o usuário está logando mas perfis não estavam na sessão (ex: re-login manual com 1 perfil), 
        // e ele precisa dos perfis no frontend:
        if (!$perfisSessao && Auth::check()) {
            // A query só é necessária se logou por outro meio e precisa da lista
            // Mas no nosso fluxo, o GoogleController os colocará lá antes.
        }

        // Realiza o login
        Auth::loginUsingId($id);
        session(['perfil_ativo' => $userParaLogar->perfil]);

        // Redireciona para o dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPerfil
{
    /**
     * Garante que o usuario autenticado possua um dos perfis permitidos.
     */
    public function handle(Request $request, Closure $next, string ...$allowedPerfis): Response
    {
        $authenticatedUser = $request->user();

        if (! $authenticatedUser) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        if (! in_array($authenticatedUser->perfil, $allowedPerfis, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Voce nao possui permissao para esta acao.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdministrador
{
    /**
     * Permite acesso apenas para o perfil estrito "Administrador".
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authenticatedUser = $request->user();

        if (! $authenticatedUser) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        if ($authenticatedUser->perfil !== 'Administrador') {
            abort(Response::HTTP_FORBIDDEN, 'Acesso permitido apenas para administradores.');
        }

        return $next($request);
    }
}

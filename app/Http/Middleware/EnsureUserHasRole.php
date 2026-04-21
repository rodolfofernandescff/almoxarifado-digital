<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->hasRole($roles)) {
            abort(Response::HTTP_FORBIDDEN, 'Você não possui permissão para acessar este recurso.');
        }

        return $next($request);
    }
}

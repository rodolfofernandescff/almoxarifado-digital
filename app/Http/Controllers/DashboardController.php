<?php

namespace App\Http\Controllers;

use App\Services\RequisicaoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly RequisicaoService $requisicaoService
    ) {
    }

    /**
     * Exibe o dashboard com métricas do sistema baseadas no perfil do usuário.
     */
    public function index(Request $request): Response
    {
        $stats = $this->requisicaoService->getDashboardStats($request->user());

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Requisicao;
use App\Services\RequisicaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RequisicaoAdminController extends Controller
{
    public function __construct(
        private readonly RequisicaoService $requisicaoService
    ) {
    }

    /**
     * Exibe o painel de requisições para análise do Almoxarife/Administrador.
     */
    public function index(): Response
    {
        $requisicoes = Requisicao::query()
            ->with([
                'usuario:id,name,email,setor',
                'itens.produto:id,nome,unidade_medida,estoque_atual',
                'aprovador:id,name',
            ])
            ->latest()
            ->paginate(15)
            ->through(fn (Requisicao $requisicao): array => [
                'id' => $requisicao->id,
                'status' => $requisicao->status,
                'justificativa' => $requisicao->justificativa,
                'observacao_admin' => $requisicao->observacao_admin,
                'solicitante' => $requisicao->usuario ? [
                    'name' => $requisicao->usuario->name,
                    'email' => $requisicao->usuario->email,
                    'setor' => $requisicao->usuario->setor,
                ] : null,
                'aprovador' => $requisicao->aprovador?->name,
                'total_itens' => $requisicao->itens->sum('quantidade_pedida'),
                'created_at' => $requisicao->created_at?->format('d/m/Y H:i'),
                'itens' => $requisicao->itens->map(fn ($item): array => [
                    'id' => $item->id,
                    'produto' => $item->produto?->nome ?? 'Produto removido',
                    'unidade_medida' => $item->produto?->unidade_medida ?? '-',
                    'quantidade_pedida' => $item->quantidade_pedida,
                    'quantidade_entregue' => $item->quantidade_entregue,
                    'estoque_atual' => $item->produto?->estoque_atual ?? 0,
                ])->values(),
            ]);

        return Inertia::render('Admin/Requisicoes/Index', [
            'requisicoes' => $requisicoes,
        ]);
    }

    /**
     * Aprova uma requisição, executando a baixa automática de estoque.
     */
    public function aprovar(Request $request, Requisicao $requisicao): RedirectResponse
    {
        $dados = $request->validate([
            'observacao' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->requisicaoService->aprovar(
                $requisicao,
                $request->user(),
                $dados['observacao'] ?? null
            );

            return Redirect::route('admin.requisicoes.index')
                ->with('success', 'Requisição #' . $requisicao->id . ' aprovada com sucesso. Estoque atualizado.');
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode(' ');

            return Redirect::route('admin.requisicoes.index')
                ->with('error', $messages);
        }
    }

    /**
     * Reprova uma requisição, gravando a justificativa obrigatória.
     */
    public function reprovar(Request $request, Requisicao $requisicao): RedirectResponse
    {
        $dados = $request->validate([
            'observacao' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $this->requisicaoService->reprovar(
                $requisicao,
                $request->user(),
                $dados['observacao']
            );

            return Redirect::route('admin.requisicoes.index')
                ->with('success', 'Requisição #' . $requisicao->id . ' reprovada.');
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode(' ');

            return Redirect::route('admin.requisicoes.index')
                ->with('error', $messages);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Requisicao;
use App\Services\RequisicaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class RequisicaoController extends Controller
{
    public function __construct(
        private readonly RequisicaoService $requisicaoService
    ) {
    }

    public function index(): Response
    {
        $requisicoes = Requisicao::query()
            ->with(['usuario:id,name,setor', 'itens:id,requisicao_id,quantidade_pedida'])
            ->latest()
            ->paginate(10)
            ->through(fn (Requisicao $requisicao): array => [
                'id' => $requisicao->id,
                'status' => $requisicao->status,
                'solicitante' => $requisicao->usuario?->name,
                'setor' => $requisicao->usuario?->setor,
                'total_itens' => $requisicao->itens->sum('quantidade_pedida'),
                'created_at' => $requisicao->created_at?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Requisicoes/Index', [
            'requisicoes' => $requisicoes,
        ]);
    }

    public function show(Requisicao $requisicao): Response
    {
        $requisicao->load([
            'usuario:id,name,email,setor',
            'aprovador:id,name',
            'itens.produto:id,nome,unidade_medida',
        ]);

        return Inertia::render('Requisicoes/Show', [
            'requisicao' => [
                'id' => $requisicao->id,
                'status' => $requisicao->status,
                'justificativa' => $requisicao->justificativa,
                'observacao_admin' => $requisicao->observacao_admin,
                'created_at' => $requisicao->created_at?->format('d/m/Y H:i'),
                'solicitante' => [
                    'name' => $requisicao->usuario?->name,
                    'email' => $requisicao->usuario?->email,
                    'setor' => $requisicao->usuario?->setor,
                ],
                'aprovador' => $requisicao->aprovador?->name,
                'itens' => $requisicao->itens->map(fn ($item): array => [
                    'id' => $item->id,
                    'produto' => $item->produto?->nome,
                    'unidade_medida' => $item->produto?->unidade_medida,
                    'quantidade_pedida' => $item->quantidade_pedida,
                    'quantidade_entregue' => $item->quantidade_entregue,
                ])->values(),
            ],
        ]);
    }

    public function meusPedidos(Request $request): Response
    {
        $usuario = $request->user();

        $produtosDisponiveis = Produto::query()
            ->with('categoria:id,nome')
            ->orderBy('nome')
            ->get(['id', 'categoria_id', 'nome', 'estoque_atual', 'unidade_medida'])
            ->map(fn (Produto $produto): array => [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'categoria' => $produto->categoria?->nome ?? '-',
                'estoque_atual' => $produto->estoque_atual,
                'unidade_medida' => $produto->unidade_medida,
            ])
            ->values();

        $minhasRequisicoes = Requisicao::query()
            ->where('user_id', $usuario->id)
            ->with('itens.produto:id,nome,unidade_medida')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Requisicao $requisicao): array => [
                'id' => $requisicao->id,
                'status' => $requisicao->status,
                'created_at' => $requisicao->created_at?->format('d/m/Y H:i'),
                'justificativa' => $requisicao->justificativa,
                'itens' => $requisicao->itens->map(fn ($item): array => [
                    'produto' => $item->produto?->nome,
                    'unidade_medida' => $item->produto?->unidade_medida,
                    'quantidade_pedida' => $item->quantidade_pedida,
                ])->values(),
            ])
            ->values();

        return Inertia::render('Requisicoes/MeusPedidos', [
            'produtos' => $produtosDisponiveis,
            'minhasRequisicoes' => $minhasRequisicoes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->requisicaoService->criarNovaRequisicao(
            $request->user(),
            $request->all()
        );

        return Redirect::route('requisicoes.meus')
            ->with('success', 'Requisição criada com sucesso.');
    }
}

<?php

namespace App\Services;

use App\Models\ItemRequisicao;
use App\Models\Produto;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RequisicaoService
{
    /**
     * Cria uma nova requisição com seus itens.
     *
     * Neste momento o estoque NÃO é decrementado. A baixa efetiva
     * acontece somente na aprovação, garantindo que apenas requisições
     * aprovadas impactem o inventário.
     *
     * @throws ValidationException
     */
    public function criarNovaRequisicao(User $usuario, array $dados): Requisicao
    {
        $dadosValidados = $this->validarEntrada($dados);

        return DB::transaction(function () use ($usuario, $dadosValidados): Requisicao {
            $requisicao = Requisicao::query()->create([
                'user_id' => (int) $usuario->id,
                'status' => 'pendente',
                'justificativa' => $dadosValidados['justificativa'] ?? null,
            ]);

            foreach ($dadosValidados['itens'] as $item) {
                $produto = Produto::query()->find((int) $item['produto_id']);

                if (! $produto) {
                    throw ValidationException::withMessages([
                        'itens' => ['Um dos produtos selecionados não está mais disponível.'],
                    ]);
                }

                $quantidadeSolicitada = (int) $item['quantidade'];

                if ($produto->estoque_atual < $quantidadeSolicitada) {
                    throw ValidationException::withMessages([
                        'itens' => [sprintf(
                            'Estoque insuficiente para "%s". Disponível: %d.',
                            $produto->nome,
                            $produto->estoque_atual
                        )],
                    ]);
                }

                ItemRequisicao::query()->create([
                    'requisicao_id' => $requisicao->id,
                    'produto_id' => $produto->id,
                    'quantidade_pedida' => $quantidadeSolicitada,
                ]);
            }

            return $requisicao->load(['itens.produto:id,nome,unidade_medida', 'usuario:id,name']);
        });
    }

    /**
     * Aprova uma requisição realizando a baixa automática de estoque.
     *
     * Executa dentro de DB::transaction com lockForUpdate para garantir
     * atomicidade e prevenir race conditions. Para cada item:
     *
     * 1. Trava o registro do produto (SELECT FOR UPDATE).
     * 2. Verifica se o estoque_atual >= quantidade_pedida.
     * 3. Decrementa o estoque_atual.
     * 4. Registra a quantidade_entregue = quantidade_pedida.
     *
     * Se algum produto não possuir estoque suficiente, a transação é
     * abortada e NENHUMA alteração é persistida (rollback automático).
     *
     * @throws ValidationException Caso a requisição já tenha sido processada ou haja estoque insuficiente.
     */
    public function aprovar(Requisicao $requisicao, User $aprovador, ?string $observacao = null): Requisicao
    {
        if ($requisicao->status !== 'pendente') {
            throw ValidationException::withMessages([
                'status' => ['Esta requisição já foi processada e não pode ser aprovada novamente.'],
            ]);
        }

        return DB::transaction(function () use ($requisicao, $aprovador, $observacao): Requisicao {
            $requisicao->load('itens');

            foreach ($requisicao->itens as $item) {
                $produto = Produto::query()
                    ->lockForUpdate()
                    ->find($item->produto_id);

                if (! $produto) {
                    throw ValidationException::withMessages([
                        'itens' => [sprintf(
                            'O produto (ID %d) não está mais cadastrado no sistema.',
                            $item->produto_id
                        )],
                    ]);
                }

                if ($produto->estoque_atual < $item->quantidade_pedida) {
                    throw ValidationException::withMessages([
                        'itens' => [sprintf(
                            'Erro: Produto "%s" não possui estoque suficiente. Disponível: %d, Solicitado: %d.',
                            $produto->nome,
                            $produto->estoque_atual,
                            $item->quantidade_pedida
                        )],
                    ]);
                }

                $produto->decrement('estoque_atual', $item->quantidade_pedida);

                $item->update([
                    'quantidade_entregue' => $item->quantidade_pedida,
                ]);
            }

            $dadosAtualizacao = [
                'status' => 'aprovado',
                'aprovado_por' => $aprovador->id,
            ];

            if ($observacao) {
                $dadosAtualizacao['observacao_admin'] = $observacao;
            }

            $requisicao->update($dadosAtualizacao);

            return $requisicao->fresh(['itens.produto:id,nome,unidade_medida', 'usuario:id,name']);
        });
    }

    /**
     * Reprova uma requisição, registrando o motivo (observação obrigatória).
     *
     * Como o estoque NÃO é decrementado na criação, a reprovação apenas
     * altera o status para 'recusado' e grava a justificativa do gestor.
     * Não há necessidade de devolver estoque.
     *
     * @throws ValidationException Caso a requisição já tenha sido processada.
     */
    public function reprovar(Requisicao $requisicao, User $reprovador, string $observacao): Requisicao
    {
        if ($requisicao->status !== 'pendente') {
            throw ValidationException::withMessages([
                'status' => ['Esta requisição já foi processada e não pode ser reprovada novamente.'],
            ]);
        }

        $requisicao->update([
            'status' => 'recusado',
            'observacao_admin' => $observacao,
            'aprovado_por' => $reprovador->id,
        ]);

        return $requisicao->fresh(['itens.produto:id,nome,unidade_medida', 'usuario:id,name']);
    }

    /**
     * @throws ValidationException
     */
    private function validarEntrada(array $dados): array
    {
        return Validator::make($dados, [
            'justificativa' => ['nullable', 'string', 'max:1000'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'integer', 'distinct', 'exists:produtos,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
        ])->validate();
    }

    /**
     * Retorna as métricas para o Dashboard baseadas no perfil do usuário.
     */
    public function getDashboardStats(User $usuario): array
    {
        $perfil = strtolower($usuario->perfil);
        $isGestor = in_array($perfil, ['administrador', 'almoxarife']);

        if ($isGestor) {
            $totalProdutos = Produto::query()->count();
            $requisicoesPendentes = Requisicao::query()->where('status', 'pendente')->count();
            $estoqueCritico = Produto::query()->whereColumn('estoque_atual', '<=', 'estoque_minimo')->count();
            
            $produtosMaisRequisitados = ItemRequisicao::query()
                ->select('produto_id', DB::raw('SUM(quantidade_pedida) as total_pedido'))
                ->with('produto:id,nome')
                ->groupBy('produto_id')
                ->orderByDesc('total_pedido')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'nome' => $item->produto->nome ?? 'Desconhecido',
                        'total' => (int) $item->total_pedido,
                    ];
                });
                
            $topEstoqueCritico = Produto::query()
                ->whereColumn('estoque_atual', '<=', 'estoque_minimo')
                ->orderBy('estoque_atual', 'asc')
                ->limit(5)
                ->get(['id', 'nome', 'estoque_atual', 'estoque_minimo', 'unidade_medida']);

            return [
                'is_gestor' => true,
                'total_produtos' => $totalProdutos,
                'requisicoes_pendentes' => $requisicoesPendentes,
                'itens_estoque_critico' => $estoqueCritico,
                'produtos_mais_requisitados' => $produtosMaisRequisitados,
                'top_estoque_critico' => $topEstoqueCritico,
            ];
        }

        // Dashboard simplificado para Requisitante
        $minhasRequisicoes = Requisicao::query()
            ->where('user_id', $usuario->id)
            ->latest()
            ->limit(5)
            ->with('itens.produto:id,nome')
            ->get(['id', 'status', 'created_at', 'justificativa']);

        $totalMinhasPendentes = Requisicao::query()
            ->where('user_id', $usuario->id)
            ->where('status', 'pendente')
            ->count();

        $totalMinhasAprovadas = Requisicao::query()
            ->where('user_id', $usuario->id)
            ->where('status', 'aprovado')
            ->count();

        return [
            'is_gestor' => false,
            'minhas_requisicoes' => $minhasRequisicoes,
            'total_pendentes' => $totalMinhasPendentes,
            'total_aprovadas' => $totalMinhasAprovadas,
        ];
    }
}

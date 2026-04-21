<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProdutoController extends Controller
{
    private const UNIDADES_MEDIDA = ['un', 'kg', 'resma'];

    public function index(): Response
    {
        $produtos = Produto::query()
            ->with('categoria:id,nome')
            ->orderBy('nome')
            ->paginate(12)
            ->through(fn (Produto $produto): array => [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'descricao' => $produto->descricao,
                'estoque_atual' => $produto->estoque_atual,
                'estoque_minimo' => $produto->estoque_minimo,
                'unidade_medida' => $produto->unidade_medida,
                'categoria' => [
                    'nome' => $produto->categoria?->nome,
                ],
            ]);

        return Inertia::render('Produtos/Index', [
            'produtos' => $produtos,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Produtos/Create', [
            'categorias' => Categoria::all(['id', 'nome']),
            'unidadesMedida' => self::UNIDADES_MEDIDA,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validateProduto($request);

        Produto::query()->create($validatedData);

        return Redirect::route('produtos.index')
            ->with('success', 'Produto cadastrado com sucesso.');
    }

    public function show(Produto $produto): Response
    {
        $produto->load('categoria:id,nome');

        return Inertia::render('Produtos/Show', [
            'produto' => [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'descricao' => $produto->descricao,
                'estoque_atual' => $produto->estoque_atual,
                'estoque_minimo' => $produto->estoque_minimo,
                'unidade_medida' => $produto->unidade_medida,
                'categoria' => [
                    'nome' => $produto->categoria?->nome,
                ],
            ],
        ]);
    }

    public function edit(Produto $produto): Response
    {
        return Inertia::render('Produtos/Edit', [
            'produto' => [
                'id' => $produto->id,
                'categoria_id' => $produto->categoria_id,
                'nome' => $produto->nome,
                'descricao' => $produto->descricao,
                'estoque_atual' => $produto->estoque_atual,
                'estoque_minimo' => $produto->estoque_minimo,
                'unidade_medida' => $produto->unidade_medida,
            ],
            'categorias' => Categoria::all(['id', 'nome']),
            'unidadesMedida' => self::UNIDADES_MEDIDA,
        ]);
    }

    public function update(Request $request, Produto $produto): RedirectResponse
    {
        $validatedData = $this->validateProduto($request);

        $produto->update($validatedData);

        return Redirect::route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Produto $produto): RedirectResponse
    {
        $produto->delete();

        return Redirect::route('produtos.index')
            ->with('success', 'Produto excluido com sucesso.');
    }

    private function validateProduto(Request $request): array
    {
        return $request->validate([
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'estoque_atual' => ['required', 'numeric', 'min:0'],
            'estoque_minimo' => ['required', 'numeric', 'min:0'],
            'unidade_medida' => ['required', 'string', 'in:un,kg,resma'],
        ]);
    }
}
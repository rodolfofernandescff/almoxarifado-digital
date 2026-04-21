<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Sem Autenticação)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Definidas em auth.php)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Rotas OAuth (Google)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
});

Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas (Disponíveis para Todos os Usuários Logados)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Principal
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Rotas Administrativas (perfil estrito: Administrador)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'perfil.admin'])->prefix('admin')->name('admin.')->group(function () {
    // Gerenciamento de Usuários
    Route::resource('users', UserController::class)->except(['show']);

    // Dashboard Admin
    Route::get('/', function () {
        return Inertia::render('Dashboard', [
            'section' => 'Admin',
        ]);
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Rotas Almoxarife (role:almoxarife)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {

    // Gerenciar Requisições/Entregas
    Route::get('/requisicoes', function () {
        return Inertia::render('Requisicoes/Index');
    })->name('requisicoes.index');

    Route::get('/requisicoes/{id}', function ($id) {
        return Inertia::render('Requisicoes/Show');
    })->name('requisicoes.show');

    // Área do Almoxarife
    Route::get('/minha-area', function () {
        return Inertia::render('Dashboard', [
            'section' => 'Almoxarife',
        ]);
    })->name('almoxarife.area');
});

/*
|--------------------------------------------------------------------------
| Rotas Requisitante (role:requisitante)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:requisitante'])->group(function () {
    // Meus Pedidos
    Route::get('/meus-pedidos', function () {
        return Inertia::render('Requisicoes/MeusPedidos');
    })->name('requisicoes.meus');

    Route::post('/meus-pedidos', function () {
        // Criar nova requisição
    })->name('requisicoes.store');

    // Área do Requisitante
    Route::get('/minha-area', function () {
        return Inertia::render('Dashboard', [
            'section' => 'Requisitante',
        ]);
    })->name('requisitante.area');
});

/*
|--------------------------------------------------------------------------
| Rotas de Produtos
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('produtos', ProdutoController::class)
        ->only(['index', 'show'])
        ->names('produtos');

    Route::middleware('perfil:Administrador,Almoxarife')->group(function () {
        Route::resource('produtos', ProdutoController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy'])
            ->names('produtos');
    });
});

/*
|--------------------------------------------------------------------------
| Rota Legada (Compatibilidade)
|--------------------------------------------------------------------------
*/

Route::get('/almoxarifado', [ProdutoController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('almoxarifado.index');

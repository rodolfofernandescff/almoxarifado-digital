<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\RequisicaoAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    // Logs do Sistema
    Route::get('/logs', [\App\Http\Controllers\LogController::class, 'index'])->name('logs.index');

    // Dashboard Admin
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Rotas Almoxarife (role:almoxarife)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::get('/requisicoes/{requisicao}', [RequisicaoController::class, 'show'])
        ->whereNumber('requisicao')
        ->name('requisicoes.show');

    // Área do Almoxarife
    Route::get('/minha-area', [DashboardController::class, 'index'])->name('almoxarife.area');
});

/*
|--------------------------------------------------------------------------
| Rotas de Gestão de Requisições (Administrador e Almoxarife)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'perfil:Administrador,Almoxarife'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/requisicoes', [RequisicaoAdminController::class, 'index'])->name('requisicoes.index');
    Route::post('/requisicoes/{requisicao}/aprovar', [RequisicaoAdminController::class, 'aprovar'])
        ->whereNumber('requisicao')
        ->name('requisicoes.aprovar');
    Route::post('/requisicoes/{requisicao}/reprovar', [RequisicaoAdminController::class, 'reprovar'])
        ->whereNumber('requisicao')
        ->name('requisicoes.reprovar');
});

/*
|--------------------------------------------------------------------------
| Rotas Requisitante (role:requisitante)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:requisitante'])->group(function () {

    // Área do Requisitante
    Route::get('/minha-area', [DashboardController::class, 'index'])->name('requisitante.area');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/meus-pedidos', [RequisicaoController::class, 'meusPedidos'])->name('requisicoes.meus');
    Route::post('/meus-pedidos', [RequisicaoController::class, 'store'])->name('requisicoes.store');
});

/*
|--------------------------------------------------------------------------
| Rotas de Produtos
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('produtos', ProdutoController::class)
        ->only(['index', 'show'])
        ->whereNumber('produto')
        ->names('produtos');

    Route::middleware('perfil:Administrador,Almoxarife')->group(function () {
        Route::resource('produtos', ProdutoController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy'])
            ->whereNumber('produto')
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

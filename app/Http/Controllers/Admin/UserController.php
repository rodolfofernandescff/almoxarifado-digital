<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    private const PERFIS_DISPONIVEIS = [
        'Administrador',
        'Almoxarife',
        'Requisitante',
    ];

    public function index(): Response
    {
        $paginatedUsers = User::query()
            ->select(['id', 'name', 'email', 'perfil', 'setor', 'created_at'])
            ->orderBy('name')
            ->paginate(10)
            ->through(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'perfil' => $user->perfil,
                'setor' => $user->setor,
                'created_at' => optional($user->created_at)->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $paginatedUsers,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'perfis' => self::PERFIS_DISPONIVEIS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'perfil' => ['required', 'string', 'in:Administrador,Almoxarife,Requisitante'],
            'setor' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::query()->create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'perfil' => $validatedData['perfil'],
            'setor' => $validatedData['setor'],
            'password' => Hash::make($validatedData['password']),
            'is_active' => true,
        ]);

        return Redirect::route('admin.users.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'perfil' => $user->perfil,
                'setor' => $user->setor,
            ],
            'perfis' => self::PERFIS_DISPONIVEIS,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'perfil' => ['required', 'string', 'in:Administrador,Almoxarife,Requisitante'],
            'setor' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $userDataToUpdate = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'perfil' => $validatedData['perfil'],
            'setor' => $validatedData['setor'],
        ];

        if (! empty($validatedData['password'])) {
            $userDataToUpdate['password'] = Hash::make($validatedData['password']);
        }

        $user->update($userDataToUpdate);

        return Redirect::route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $authenticatedUserId = Auth::id();

        if ($authenticatedUserId === $user->id) {
            return Redirect::route('admin.users.index')
                ->with('success', 'Não é permitido excluir o próprio usuário logado.');
        }

        $user->delete();

        return Redirect::route('admin.users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}

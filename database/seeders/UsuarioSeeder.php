<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário de teste para autenticação Google
        User::create([
            'nome' => 'Usuário Teste',
            'email_institucional' => 'teste@gmail.com', // Altere para seu email Google
            'email' => 'teste@gmail.com',
            'tipo' => 'Administrador',
            'status' => 1,
            'regional_id' => 1,
            'password' => Hash::make('password123'), // Caso precise
        ]);

        // Criar mais alguns usuários de exemplo
        User::create([
            'nome' => 'João Silva',
            'email_institucional' => 'joao@empresa.com',
            'email' => 'joao@empresa.com',
            'tipo' => 'Gerente',
            'status' => 1,
            'regional_id' => 1,
        ]);

        User::create([
            'nome' => 'Maria Santos',
            'email_institucional' => 'maria@empresa.com',
            'email' => 'maria@empresa.com',
            'tipo' => 'Usuario',
            'status' => 1,
            'regional_id' => 2,
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AlmoxarifadoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar um Usuário Administrador para você testar
        User::create([
            'name' => 'Rodolfo Administrador',
            'email' => 'admin@teste.com',
            'password' => Hash::make('123456'), // Senha simples para teste
            'perfil' => 'Administrador',
            'setor' => 'TI',
        ]);

        // 2. Criar Categorias
        $papelariaId = DB::table('categorias')->insertGetId([
            'nome' => 'Papelaria',
            'slug' => 'papelaria',
            'created_at' => now(),
        ]);

        $informaticaId = DB::table('categorias')->insertGetId([
            'nome' => 'Informática',
            'slug' => 'informatica',
            'created_at' => now(),
        ]);

        // 3. Criar Produtos dentro das categorias
        DB::table('produtos')->insert([
            [
                'categoria_id' => $papelariaId,
                'nome' => 'Papel A4 Report 500fls',
                'estoque_atual' => 50,
                'estoque_minimo' => 10,
                'unidade_medida' => 'resma',
                'created_at' => now(),
            ],
            [
                'categoria_id' => $papelariaId,
                'nome' => 'Caneta Esferográfica Azul',
                'estoque_atual' => 100,
                'estoque_minimo' => 20,
                'unidade_medida' => 'un',
                'created_at' => now(),
            ],
            [
                'categoria_id' => $informaticaId,
                'nome' => 'Toner HP 105A Black',
                'estoque_atual' => 5,
                'estoque_minimo' => 2,
                'unidade_medida' => 'un',
                'created_at' => now(),
            ],
        ]);
    }
}
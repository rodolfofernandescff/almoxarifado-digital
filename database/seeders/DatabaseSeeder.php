<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['display_name' => 'Administrador']
        );

        Role::updateOrCreate(
            ['name' => 'gestor', 'guard_name' => 'web'],
            ['display_name' => 'Gestor']
        );

        Role::updateOrCreate(
            ['name' => 'usuario', 'guard_name' => 'web'],
            ['display_name' => 'Usuário']
        );

        $masterUser = User::updateOrCreate(
            ['email' => 'rodolfocff@gmail.com'],
            [
                'name' => 'Rodolfo',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $masterUser->syncRoles([$adminRole->name]);
    }
}

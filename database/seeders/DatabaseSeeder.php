<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->state(['email' => 'admin@admin.com', 'password' => Hash::make('test')])
            ->has(
                Role::factory(['slug' => 'admin'])
                    ->hasPermissions(['name' => 'all', 'slug' => '*'])
            )
            ->create();

        $permissions = [
            ['name' => 'Registrar usuario',     'slug' => 'user.register'],
            ['name' => 'Actualizar usuario',    'slug' => 'user.update'],
            ['name' => 'Eliminar usuario',      'slug' => 'user.delete'],
            ['name' => 'Crear rol',             'slug' => 'role.store'],
            ['name' => 'Actualizar rol',        'slug' => 'role.update'],
            ['name' => 'Eliminar rol',          'slug' => 'role.destroy'],
            ['name' => 'Crear permiso',         'slug' => 'permission.store'],
            ['name' => 'Actualizar permiso',    'slug' => 'permission.update'],
            ['name' => 'Eliminar permiso',      'slug' => 'permission.destroy'],
            ['name' => 'Crear categoria',       'slug' => 'category.store'],
            ['name' => 'Actualizar categoria',  'slug' => 'category.update'],
            ['name' => 'Eliminar categoria',    'slug' => 'category.destroy'],
            ['name' => 'Ver proveedores',       'slug' => 'provider.view'],
            ['name' => 'Crear proveedor',       'slug' => 'provider.store'],
            ['name' => 'Actualizar proveedor',  'slug' => 'provider.update'],
            ['name' => 'Eliminar proveedor',    'slug' => 'provider.destroy'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

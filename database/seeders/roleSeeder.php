<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class roleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'Administrador del sistema',     'slug' => 'admin'],
            ['name' => 'Administrador de seguridad',    'slug' => 'security.admin'],
            ['name' => 'Administrador de cuentas',      'slug' => 'account.manager'],
            ['name' => 'Coordinador',                   'slug' => 'coordinator'],
            ['name' => 'Desarrollador',                 'slug' => 'developer'],
            ['name' => 'Vendedor',                      'slug' => 'seller'],
            ['name' => 'Cliente',                       'slug' => 'client'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}

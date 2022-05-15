<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class permissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['name' => 'Todos los permisos',            'slug' => '*'],
            //user
            ['name' => 'Ver informacion usuario',       'slug' => 'user.view'],
            ['name' => 'Registrar usuario',             'slug' => 'user.register'],
            ['name' => 'Actualizar usuario',            'slug' => 'user.update'],
            ['name' => 'Actualizar roles usuario',      'slug' => 'user.change.role'],
            ['name' => 'Eliminar usuario',              'slug' => 'user.delete'],
            //role
            ['name' => 'Crear rol',                     'slug' => 'role.store'],
            ['name' => 'Actualizar rol',                'slug' => 'role.update'],
            ['name' => 'Eliminar rol',                  'slug' => 'role.destroy'],
            //permission
            ['name' => 'Crear permiso',                 'slug' => 'permission.store'],
            ['name' => 'Actualizar permiso',            'slug' => 'permission.update'],
            ['name' => 'Eliminar permiso',              'slug' => 'permission.destroy'],
            //category
            ['name' => 'Crear categoria',               'slug' => 'category.store'],
            ['name' => 'Actualizar categoria',          'slug' => 'category.update'],
            ['name' => 'Eliminar categoria',            'slug' => 'category.destroy'],
            //provider
            ['name' => 'Ver informacion proveedores',   'slug' => 'provider.view'],
            ['name' => 'Crear proveedor',               'slug' => 'provider.store'],
            ['name' => 'Actualizar proveedor',          'slug' => 'provider.update'],
            ['name' => 'Eliminar proveedor',            'slug' => 'provider.destroy'],
            //product
            ['name' => 'Crear productos',               'slug' => 'product.store'],
            ['name' => 'Actualizar productos',          'slug' => 'product.update'],
            ['name' => 'Eliminar productos',            'slug' => 'product.destroy'],
            //client
            ['name' => 'Ver informacion de clientes',   'slug' => 'client.view'],
            ['name' => 'Añadir clientes',               'slug' => 'client.store'],
            ['name' => 'Actualizar clientes',           'slug' => 'client.update'],
            ['name' => 'Eliminar clientes',             'slug' => 'client.destroy'],
            //purchase
            ['name' => 'Ver informacion de compras',     'slug' => 'purchase.view'],
            ['name' => 'Añadir compras',                 'slug' => 'purchase.store'],
            ['name' => 'Actualizar compras',             'slug' => 'purchase.update'],
            ['name' => 'Eliminar compras',               'slug' => 'purchase.destroy'],
            //sale
            ['name' => 'Ver informacion de ventas',     'slug' => 'sale.view'],
            ['name' => 'Añadir ventas',                 'slug' => 'sale.store'],
            ['name' => 'Actualizar ventas',             'slug' => 'sale.update'],
            ['name' => 'Eliminar ventas',               'slug' => 'sale.destroy'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

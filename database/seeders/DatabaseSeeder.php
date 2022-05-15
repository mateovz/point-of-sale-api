<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Provider;
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
        $this->call([
            permissionSeeder::class,
            roleSeeder::class,
        ]);

        $this->createAdmin();
        $this->createUsers();
    }

    private function createAdmin():void{
        $admin = User::factory(['email' => 'admin@admin.com'])->create();
        $role = Role::whereSlug('admin')->first();
        $permission = Permission::whereSlug('*')->first();
        $admin->roles()->attach($role->id);
        $role->permissions()->attach($permission->id);
    }

    private function createUsers():void{
        $users = User::factory(10)->create();
        $roles = Role::all()->except(1);
        foreach ($users as $user) {
            $role = $roles[random_int(0, (count($roles) - 1))];
            $user->roles()->attach($role->id);
        }
    }
}

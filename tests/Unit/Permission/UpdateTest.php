<?php

namespace Tests\Unit\Permission;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_update(){
        $permission = Permission::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $permission->name,
            'slug'  => $this->faker->word
        ];
        $this->put(route('permission.update', ['permission' => $permission->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status'    => 'success',
                'permission'      => ['id' => $permission->id]
            ]);
        $this->assertDatabaseHas('permissions', $data);
    }

    public function test_invalid_data(){
        $permission = Permission::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = ['name' => null];
        $this->put(route('permission.update', ['permission' => $permission->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_name_or_slug_exists(){
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => [
                'name'  => $permission1->name,
                'slug'  => $this->faker->slug(2)
            ],
            'slug'  => [
                'name'  => $this->faker->word,
                'slug'  => $permission1->slug
            ]
        ];
        foreach ($data as $key => $value) {
            $this->put(route('permission.update', ['permission' => $permission2->id]), $value, [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token
            ])->assertStatus(400)
                ->assertJson(['status'  => 'error'])
                ->assertJsonCount(1, 'errors.'.$key);
        }
    }

    public function test_permission_does_not_exists(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $this->faker->word,
            'slug'  => $this->faker->slug(2),
        ];
        $this->put(route('permission.update', ['permission' => random_int(10, 20)]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}

<?php

namespace Tests\Unit\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateAvatarTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_upload_avatar(){
        Storage::fake('local');
        $file = UploadedFile::fake()->image('default.jpg');
        $user = User::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;

        $data = [
            'avatar' => $file
        ];
        $this->post(route('user.update', ['user' => $user->id]), 
            $data, 
            [
                'Accept'        => 'application/json',
                'enctype'       => 'multipart/form-data',
                'Authorization' => 'Bearer '.$token
            ]
        )->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'user' => ['avatar']
            ]);
        Storage::assertExists('/public/avatars/'.$file->hashName());
    }

    public function test_delete_old_avatar(){
        Storage::fake('local');
        $oldFile = UploadedFile::fake()
            ->image('old.jpg');
        $oldUrl = Storage::disk('avatars')->append('', $oldFile);
        $oldUrl = Storage::url($oldUrl);
        $user = User::factory(['avatar' => $oldUrl])->create();
        $newFile = UploadedFile::fake()->image('default.jpg');
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;

        $data = [
            'avatar' => $newFile
        ];
        $this->post(route('user.update', ['user' => $user->id]), 
            $data, 
            [
                'Accept'        => 'application/json',
                'enctype'       => 'multipart/form-data',
                'Authorization' => 'Bearer '.$token
            ]
        )->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'user' => ['avatar']
            ]);
        $oldUrl = str_replace('storage', 'public', $oldUrl);
        Storage::assertMissing($oldUrl);
    }
}

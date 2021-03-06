<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request){
        $users = User::all()->except($request->user()->id);
        foreach ($users as $user) {
            $user->permissions();
        }
        return response()->json([
            'status'    => 'success',
            'users'     => $users
        ], 200);
    }

    public function show($id){
        $user = User::find($id);
        if(is_null($user)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['user' => ['Does not exist.']]
            ], 400);
        }
        $user->permissions();
        return response()->json([
            'status'    => 'success',
            'user'      => $user
        ], 200);
    }

    public function login(LoginRequest $request){
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        if(!Hash::check($data['password'], $user->password)){
            return response()->json([
                'status'    => 'error',
                'errors'    => [
                    'email' => ['Invalid password or email.']
                ]
            ], 401);
        }
        $permissions = $user->permissions();
        $device = $user['device'] ?? 'default';
        $user->tokens()->where('name', $device)->delete();
        $token = $user->createToken($device, $permissions)->plainTextToken;
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user'  => $user
        ], 200);
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success'
        ], 200);
    }

    public function register(RegisterRequest $request){
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        if($request->user()->tokenCan('user.change.role')){
            if(isset($data['roles']['add'])) $this->addRoles($user, $data['roles']['add']);
        }
        if($request->hasFile('avatar')){
            $user = $this->uploadAvatar($user, $request->file('avatar'));
        }
        return response()->json([
            'status'    => 'success'
        ], 200);
    }

    public function update($id, UpdateRequest $request){
        $data = $request->validated();
        $user = User::find($id);
        if(is_null($user)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['user' => ['Does not exist.']]
            ], 400);
        }
        $user->update($data);
        if($request->user()->tokenCan('user.change.role')){
            if(isset($data['roles']['add'])) $this->addRoles($user, $data['roles']['add']);
            if(isset($data['roles']['remove'])) $this->removeRoles($user, $data['roles']['remove']);
        }
        if($request->hasFile('avatar')){
            $user = $this->uploadAvatar($user, $request->file('avatar'));
        }
        return response()->json([
            'status'    => 'success',
            'user'      => $user
        ], 200);
    }

    public function delete($id){
        $user = User::find($id);
        if(is_null($user)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['user' => ['Does not exist.']]
            ], 400);
        }
        $user->delete();
        return response()->json([
            'status'    => 'success'
        ], 200);
    }

    private function addRoles(User $user, array $roles):void{
        foreach ($roles as $role) {
            if(!is_object($user->roles()->find($role['id']))){
                $user->roles()->attach($role['id']);
            }
        }
    }

    private function removeRoles(User $user, array $roles):void{
        foreach ($roles as $role) {
            $user->roles()->detach($role['id']);
        }
    }

    private function uploadAvatar(User $user, UploadedFile $avatar):User{
        if($user->avatar){
            $urlOld = str_replace('storage', 'public', $user->avatar);
            if(Storage::exists($urlOld)){
                Storage::delete($urlOld);
            }
        }
        $image = $avatar->store('public/avatars');
        $url = Storage::url($image);
        $user->update(['avatar' => $url]);
        return $user;
    }
}
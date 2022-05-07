<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
        $token = $user->createToken($user['device'] ?? 'default', $permissions)->plainTextToken;
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
        User::create($data);
        return response()->json([
            'status'    => 'success'
        ], 200);
    }

    public function update(UpdateRequest $request){
        $data = $request->validated();
        $user = $request->user();
        $user->update($data);
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
}

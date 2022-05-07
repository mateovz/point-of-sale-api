<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index');
        $this->middleware('ability:role.store')->only('store');
        $this->middleware('ability:role.update')->only('update');
        $this->middleware('ability:role.destroy')->only('destroy');    
    }
    
    public function index(Request $request){
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->permissions;
        }
        return response()->json([
            'status'    => 'success',
            'roles'     => $roles
        ], 200);
    }

    public function store(StoreRequest $request){
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'], '.');
        $role = Role::create($data);
        return response()->json([
            'status'    => 'success',
            'role'      => $role
        ], 200);
    }

    public function update($id, UpdateRequest $request){
        $role = Role::find($id);
        if(is_null($role)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['role' => ['Does not exist.']]
            ], 400);
        }
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'], '.');
        $role->update($data);
        return response()->json([
            'status'    => 'success',
            'role'      => $role
        ]);
    }

    public function destroy($id){
        $role = Role::find($id);
        if(is_null($role)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['role' => ['Does not exist.']]
            ], 400);
        }
        $role->delete();
        return response()->json([
            'status'    => 'success'
        ], 200);
    }
}

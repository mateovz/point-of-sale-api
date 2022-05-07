<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\StoreRequest;
use App\Http\Requests\Permission\UpdateRequest;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index');
        $this->middleware('ability:permission.store')->only('store');
        $this->middleware('ability:permission.update')->only('update');
        $this->middleware('ability:permission.destroy')->only('destroy');    
    }
    
    public function index(Request $request){
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $permission->roles;
        }
        return response()->json([
            'status'        => 'success',
            'permissions'   => $permissions
        ], 200);
    }

    public function store(StoreRequest $request){
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'], '.');
        $permission = Permission::create($data);
        return response()->json([
            'status'        => 'success',
            'permission'    => $permission
        ], 200);
    }

    public function update($id, UpdateRequest $request){
        $permission = Permission::find($id);
        if(is_null($permission)){
            return response()->json([
                'status' => 'error',
                'errors'    => ['permission' => ['Does not exist.']]
            ], 400);
        }
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'], '.');
        $permission->update($data);
        return response()->json([
            'status'        => 'success',
            'permission'    => $permission
        ], 200);
    }

    public function destroy($id){
        $permission = Permission::find($id);
        if(is_null($permission)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['permission' => ['Does not exist.']]
            ], 400);
        }
        $permission->delete();
        return response()->json([
            'status' => 'success'
        ], 200);
    }
}

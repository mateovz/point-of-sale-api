<?php

namespace App\Http\Controllers;

use App\Http\Requests\Provider\StoreRequest;
use App\Http\Requests\Provider\UpdateRequest;
use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('ability:provider.view')->only('index', 'show');
        $this->middleware('ability:provider.store')->only('store');
        $this->middleware('ability:provider.update')->only('update');
        $this->middleware('ability:provider.destroy')->only('destroy');
    }

    public function index(){
        return response()->json([
            'status'    => 'success',
            'providers' => Provider::all()
        ], 200);
    }

    public function show($id){
        $provider = Provider::find($id);
        if(is_null($provider)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['provider' => ['Does not exist.']]
            ], 400);
        }
        return response()->json([
            'status'    => 'success',
            'provider'  => $provider
        ], 200);
    }

    public function store(StoreRequest $request){
        $provider = Provider::create($request->validated());
        return response()->json([
            'status'    => 'success',
            'provider'  => $provider
        ], 200);
    }

    public function update($id, UpdateRequest $request){
        $provider = Provider::find($id);
        if(is_null($provider)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['provider' => ['Does not exist.']]
            ], 400);
        }
        $provider->update($request->validated());
        return response()->json([
            'status'    => 'success',
            'provider'  => $provider
        ], 200);
    }

    public function destroy($id){
        $provider = Provider::find($id);
        if(is_null($provider)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['provider' => ['Does not exist.']]
            ], 400);
        }
        $provider->delete();
        return response()->json([
            'status'    => 'success'
        ], 200);
    }
}

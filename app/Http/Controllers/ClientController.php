<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\StoreRequest;
use App\Http\Requests\Client\UpdateRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('ability:client.view')->only('index', 'show');
        $this->middleware('ability:client.store')->only('store');
        $this->middleware('ability:client.update')->only('update');
        $this->middleware('ability:client.destroy')->only('destroy');
    }

    public function index(Request $request){
        return response()->json([
            'status'    => 'success',
            'clients'  => Client::all()
        ], 200);
    }

    public function show($id){
        $client = Client::find($id);
        if(is_null($client)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['client' => ['Does not exist.']]
            ], 400);
        }
        return response()->json([
            'status'    => 'success',
            'client'    => $client
        ], 200);
    }

    public function store(StoreRequest $request){
        $client = Client::create($request->validated());
        return response()->json([
            'status'    => 'success',
            'client'  => $client
        ], 200);
    }

    public function update($id, UpdateRequest $request){
        $client = Client::find($id);
        if(is_null($client)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['client' => ['Does not exist.']]
            ], 400);
        }
        $client->update($request->validated());
        return response()->json([
            'status'    => 'success',
            'client'  => $client
        ], 200);
    }

    public function destroy($id){
        $client = Client::find($id);
        if(is_null($client)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['client' => ['Does not exist.']]
            ], 400);
        }
        $client->delete();
        return response()->json([
            'status'    => 'success'
        ], 200);
    }
}

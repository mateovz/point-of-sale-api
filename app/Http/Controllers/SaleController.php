<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sale\StoreRequest;
use App\Http\Requests\Sale\UpdateRequest;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('ability:sale.store')->only('store');
        $this->middleware('ability:sale.update')->only('update');
        $this->middleware('ability:sale.destroy')->only('destroy');
    }

    public function index(Request $request){
        $sales = Sale::all();
        return response()->json([
            'status'    => 'success',
            'sales' => $sales
        ], 200);
    }

    public function show($id, Request $request){
        $sale = Sale::find($id);
        if(is_null($sale)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['sale' => ['Does not exist.']]
            ], 400);
        }
        return response()->json([
            'status'    => 'success',
            'sale'  => $sale
        ], 200);
    }

    public function store(StoreRequest $request){
        $data = $request->validated();
        $data['total'] = 0;
        $sale = Sale::create($data);
        return response()->json([
            'status'    => 'success',
            'sale'  => $sale
        ], 200);
    }

    public function update($id, UpdateRequest $request){
        $sale = Sale::find($id);
        if(is_null($sale)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['sale' => ['Does not exist.']]
            ], 400);
        }
        
        $data = $request->validated();
        $sale->update($data);
        return response()->json([
            'status'    => 'success',
            'sale'  => $sale
        ], 200);
    }

    public function destroy($id){
        $sale = Sale::find($id);
        if(is_null($sale)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['sale' => ['Does not exist.']]
            ], 400);
        }
        $sale->delete();
        return response()->json([
            'status'    => 'success'
        ], 200);
    }
}

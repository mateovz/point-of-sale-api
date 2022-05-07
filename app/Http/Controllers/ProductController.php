<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('ability:product.store')->only('store');
        $this->middleware('ability:product.update')->only('update');
        $this->middleware('ability:product.destroy')->only('destroy');
    }

    public function index(Request $request){
        $products = Product::all();
        foreach ($products as $key => $product) {
            $products[$key] = $this->getExtraInfo($product, $request->user());
        }
        return response()->json([
            'status'    => 'success',
            'products'  => $products
        ], 200);
    }

    public function store(StoreRequest $request){
        $product = Product::create($request->validated());
        $product = $this->getExtraInfo($product, $request->user());
        return response()->json([
            'status'    => 'success',
            'product'  => $product
        ], 200);
    }

    public function update($id, UpdateRequest $request){
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['product' => ['Does not exist.']]
            ], 400);
        }
        $product->update($request->validated());
        $product = $this->getExtraInfo($product, $request->user());
        return response()->json([
            'status'    => 'success',
            'product'  => $product
        ], 200);
    }

    public function destroy($id){
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['product' => ['Does not exist.']]
            ], 400);
        }
        $product->delete();
        return response()->json([
            'status'    => 'success'
        ], 200);
    }

    private function getExtraInfo(Product $product, User $user):array{
        $product->category;
        if($user->tokenCan('provider.view')){
            $product->provider;
        }else{
            $product = array_merge($product->toArray(),
                ['provider' => $product->provider()->select('name')->first()]
            );
        }
        if(is_object($product)){
            return $product->toArray();
        }
        return $product;
    }
}

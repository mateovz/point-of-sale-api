<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sale\StoreRequest;
use App\Http\Requests\Sale\UpdateRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
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
        foreach ($sales as $key => $sale) {
            $sales[$key] = $this->getSaleInfo($sale, $request->user());
        }
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
        $sale = $this->getSaleInfo($sale, $request->user());
        return response()->json([
            'status'    => 'success',
            'sale'  => $sale
        ], 200);
    }

    public function store(StoreRequest $request){
        $data = $request->validated();
        $data['total'] = 0;

        $sale = Sale::create($data);
        $this->createSaleDetails($sale, $data['products']);

        $total = $this->calculateTotal($sale);
        $sale->update(['total' => $total]);

        $sale = $this->getSaleInfo($sale, $request->user());
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

        if(isset($data['products'])){
            foreach ($data['products'] as $product) {
                $this->updateSaleDetails($sale, $product);
            }
            
            $total = $this->calculateTotal($sale);
            $sale->update(['total' => $total]);
        }

        $sale = $this->getSaleInfo($sale, $request->user());
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

    private function getSaleInfo(Sale $sale, User $user):array{
        $sale->saleDetails;
        $sale = array_merge(
            $sale->toArray(),
            [
                'user'      => $this->getUserInfo($sale, $user),
                'client'    => $this->getClientInfo($sale, $user)
            ]
        );
        return $sale;
    }

    private function getUserInfo(Sale $sale, User $user):array{
        if($user->tokenCan('user.view')){
            return $sale->user()->first()->toArray();
        }
        return ['name' => $sale->user->name];
    }

    private function getClientInfo(Sale $sale, User $user):array{
        if($user->tokenCan('client.view')){
            return $sale->client()->first()->toArray();
        }
        return ['name' => $sale->client->name];
    }

    private function createSaleDetails(Sale $sale, array $products):void{
        foreach ($products as $product) {
            $newSaleDetail = array_merge($product, [
                'sale_id'   => $sale->id
            ]);
            if(!isset($product['price'])){
                $newSaleDetail['price'] = Product::find($product['product_id'])->price;
            }
            SaleDetail::create($newSaleDetail);
        }
    }

    private function updateSaleDetails(Sale $sale, array $product):void{
        $detail = $sale->saleDetails()->where('id', $product['product_id'])->first();
        if(is_null($detail)){
            $this->createSaleDetails($sale, [$product]);
        }else{
            $detail->update($product);
        }
    }

    private function calculateTotal(Sale $sale):float{
        $total = 0;
        foreach ($sale->saleDetails as $detail) {
            $newTotal = ($detail->price * $detail->quantity);
            $discount = $newTotal * ($detail->discount / 100);
            $total += ($newTotal - $discount);
        }
        return $total;
    }
}

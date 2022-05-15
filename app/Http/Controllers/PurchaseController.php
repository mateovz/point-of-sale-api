<?php

namespace App\Http\Controllers;

use App\Http\Requests\Purchase\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\User;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('ability:purchase.view')->only('index', 'show');
        $this->middleware('ability:purchase.store')->only('store');
        $this->middleware('ability:purchase.update')->only('update');
        $this->middleware('ability:purchase.destroy')->only('destroy');
    }

    public function index(Request $request){
        $purchases = Purchase::all();
        foreach ($purchases as $key => $purchase) {
            $purchases[$key] = $this->getPurchaseInfo($purchase, $request->user());
        }
        return response()->json([
            'status'    => 'success',
            'purchases' => $purchases
        ], 200);
    }

    public function show($id, Request $request){
        $purchase = Purchase::find($id);
        if(is_null($purchase)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['purchase' => ['Does not exist.']]
            ], 400);
        }

        $purchase = $this->getPurchaseInfo($purchase, $request->user());

        return response()->json([
            'status'    => 'success',
            'purchase'  => $purchase
        ], 200);
    }

    public function store(StoreRequest $request){
        $data = $request->validated();
        $data['total'] = 0;

        $purchase = Purchase::create($data);
        $this->createPurchaseDetails($purchase, $data['products']);

        $total = $this->calculateTotal($purchase);
        $purchase->update(['total' => $total]);

        $purchase = $this->getPurchaseInfo($purchase, $request->user());

        return response()->json([
            'status'    => 'success',
            'purchase'  => $purchase
        ], 200);
    }

    public function update($id, UpdateRequest $request){
        $purchase = Purchase::find($id);
        if(is_null($purchase)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['purchase' => ['Does not exist.']]
            ], 400);
        }
        
        $data = $request->validated();
        $purchase->update($data);

        if(isset($data['products'])){
            foreach ($data['products'] as $product) {
                $this->updatePurchaseDetails($purchase, $product);
            }
            
            $total = $this->calculateTotal($purchase);
            $purchase->update(['total' => $total]);
        }

        $purchase = $this->getPurchaseInfo($purchase, $request->user());

        return response()->json([
            'status'    => 'success',
            'purchase'  => $purchase
        ], 200);
    }

    public function destroy($id){
        $purchase = Purchase::find($id);
        if(is_null($purchase)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['purchase' => ['Does not exist.']]
            ], 400);
        }
        $purchase->delete();
        return response()->json([
            'status'    => 'success'
        ], 200);
    }

    private function getPurchaseInfo(Purchase $purchase, User $user):array{
        $purchase->purchaseDetails;
        $purchase = array_merge(
            $purchase->toArray(),
            [
                'user' => $this->getUserInfo($purchase, $user),
                'provider' => $this->getProviderInfo($purchase, $user)
            ],
        );
        return $purchase;
    }

    private function getUserInfo(Purchase $purchase, User $currentUser):array{
        if($currentUser->tokenCan('user.view')){
            return $purchase->user()->first()->toArray();
        }
        return ['name' => $purchase->user->name];
    }

    private function getProviderInfo(Purchase $purchase, User $currentUser):array{
        if($currentUser->tokenCan('provider.view')){
            return $purchase->provider()->first()->toArray();
        }
        return ['name' => $purchase->provider->name];
    }

    private function createPurchaseDetails(Purchase $purchase, array $products):void{
        foreach ($products as $product) {
            $newPurchaseDetail = array_merge($product, [
                'purchase_id' => $purchase->id
            ]);
            if(!isset($product['price'])){
                $newPurchaseDetail['price'] = Product::find($product['product_id'])->price;
            }
            PurchaseDetail::create($newPurchaseDetail);
        }
    }

    private function updatePurchaseDetails(Purchase $purchase, array $product):void{
        $detail = $purchase->purchaseDetails()->where('id', $product['product_id'])->first();
        if(is_null($detail)){
            $this->createPurchaseDetails($purchase, [$product]);
        }else{
            $detail->update($product);
        }
    }

    private function calculateTotal(Purchase $purchase):float{
        $total = 0;
        foreach ($purchase->purchaseDetails as $detail) {
            $total += ($detail->price * $detail->quantity);
        }
        return $total;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Purchase\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('ability:purchase.store')->only('store');
        $this->middleware('ability:purchase.update')->only('update');
        $this->middleware('ability:purchase.destroy')->only('destroy');
    }

    public function index(Request $request){
        $purchases = Purchase::all();
        foreach ($purchases as $key => $purchase) {
            $tables = ['user', 'provider'];
            $purchases[$key] = $this->getExtraInfo($purchase, $tables, $request->user());
        }
        return response()->json([
            'status'    => 'success',
            'purchases' => $purchases
        ], 200);
    }

    public function store(StoreRequest $request){
        $purchase = Purchase::create($request->validated());
        $tables = ['user', 'provider'];
        $purchase = $this->getExtraInfo($purchase, $tables, $request->user());
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
        $purchase->update($request->validated());
        $tables = ['user', 'provider'];
        $purchase = $this->getExtraInfo($purchase, $tables, $request->user());
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

    private function getExtraInfo(Purchase $purchase, array $tables, User $user):array{
        $purchaseExtra = $purchase->toArray();
        foreach ($tables as $table) {
            if($user->tokenCan($table.'.view')){
                $purchaseExtra = array_merge($purchaseExtra, [$table => $purchase->$table]);
            }else{
                $purchaseExtra = array_merge($purchaseExtra,
                    [$table => $purchase->$table()->select('name')->first()]
                );
            }
        }
        return $purchaseExtra;
    }
}

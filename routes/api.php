<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->controller(UserController::class)->group(function(){
    Route::post('login', 'login')->name('user.login');

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('logout', 'logout')->name('user.logout');

        Route::get('', 'index')
            ->name('user.index')
            ->middleware('ability:user.view');

        Route::get('{user}', 'show')
            ->name('user.show')
            ->middleware('ability:user.view');

        Route::post('register', 'register')
            ->name('user.register')
            ->middleware('ability:user.register');
        
        Route::post('{user}', 'update')
            ->name('user.update')
            ->middleware('ability:user.update');    

        Route::delete('{user}', 'delete')
            ->name('user.delete')
            ->middleware('ability:user.delete');
    });
});

Route::apiResources([
    'role'          => RoleController::class,
    'permission'    => PermissionController::class,
    'category'      => CategoryController::class,
    'provider'      => ProviderController::class,
    'product'       => ProductController::class,
    'client'        => ClientController::class,
    'purchase'      => PurchaseController::class,
    'sale'          => SaleController::class
]);
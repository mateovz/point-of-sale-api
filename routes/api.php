<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->controller(UserController::class)->group(function(){
    Route::post('login', 'login')->name('user.login');

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('logout', 'logout')->name('user.logout');

        Route::post('register', 'register')
            ->name('user.register')
            ->middleware('ability:user.register');
        
        Route::put('update', 'update')
            ->name('user.update')
            ->middleware('ability:user.update');    

        Route::delete('delete/{id}', 'delete')
            ->name('user.delete')
            ->middleware('ability:user.delete');
    });
});

Route::resource('role', RoleController::class)
    ->names('role')
    ->except(['create', 'show', 'edit']);

Route::resource('permission', PermissionController::class)
    ->names('permission')
    ->except(['create', 'show', 'edit']);

Route::resource('category', CategoryController::class)
    ->names('category')
    ->except(['create', 'show', 'edit']);

Route::resource('provider', ProviderController::class)
    ->names('provider')
    ->except(['create', 'show', 'edit']);

Route::resource('product', ProductController::class)
    ->names('product')
    ->except(['create', 'show', 'edit']);

Route::resource('product', ProductController::class)
    ->names('product')
    ->except(['create', 'show', 'edit']);

Route::resource('client', ClientController::class)
    ->names('client')
    ->except(['create', 'show', 'edit']);

Route::resource('purchase', PurchaseController::class)
    ->names('purchase')
    ->except(['create', 'show', 'edit']);
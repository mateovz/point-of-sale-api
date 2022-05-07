<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index');
        $this->middleware('ability:category.store')->only('store');
        $this->middleware('ability:category.update')->only('update');
        $this->middleware('ability:category.destroy')->only('destroy');
    }

    public function index(){
        return response()->json([
            'status'        => 'success',
            'categories'    => Category::all()
        ], 200);
    }

    public function store(StoreRequest $request){
        $category = Category::create($request->validated());
        return response()->json([
            'status'    => 'success',
            'category'  => $category
        ], 200);
    }

    public function update($id, UpdateRequest $request){
        $category = Category::find($id);
        if(is_null($category)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['category' => ['Does not exist.']]
            ], 400);
        }
        $category->update($request->validated());
        return response()->json([
            'status'    => 'success',
            'category'  => $category
        ], 200);
    }

    public function destroy($id){
        $category = Category::find($id);
        if(is_null($category)){
            return response()->json([
                'status'    => 'error',
                'errors'    => ['category' => ['Does not exist.']]
            ], 400);
        }
        $category->delete();
        return response()->json([
            'status'    => 'success'
        ], 200);
    }
}

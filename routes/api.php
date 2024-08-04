<?php

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Resturant;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/resturant/{id}/categories',function($id){
    $categories = Category::where('resturant_id',$id)
    ->get(['id','resturant_id','name','price','availability','description','type','discount','path']);
    return response()->json($categories);
});
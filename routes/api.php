<?php

use App\Jobs\AddOrder;
use App\Jobs\AddOrderMonthly;
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

Route::post('/resturant/order', function(Request $request) {
    $validated = $request->validate([
        'resturantId' => 'required|integer|exists:resturants,id',
        'tableNumber' => 'required|integer|min:1',
        'orderContents' => 'required', 
    ], [
        'resturantId.required' => 'Restaurant ID is required!',
        'resturantId.integer' => 'Restaurant ID should be a number!',
        'resturantId.exists' => 'Restaurant ID does not match with restaurants table records!',
        'tableNumber.required' => 'Table number is required!',
        'tableNumber.integer' => 'Table number should be a number!',
        'tableNumber.min' => 'Table number should be at least 1!',
        'orderContents.required' => 'Order contents are required!',
    ]);

    $resturant = Resturant::find($validated['resturantId']);
    
    if ($resturant && in_array($resturant->servic_type, ['monthly', 'just_menu_monthly'])) {
        AddOrderMonthly::dispatch($validated['resturantId'], $validated['tableNumber'], $validated['orderContents']);
    } else {
        AddOrder::dispatch($validated['resturantId'], $validated['tableNumber'], $validated['orderContents']);
    }

    return response()->json([
        'message' => 'Order sent successfully!',
    ]);
});


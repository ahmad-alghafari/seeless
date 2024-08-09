<?php

use App\Jobs\AddOrder;
use App\Jobs\AddOrderMonthly;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Resturant;
use App\Models\Order;
use App\Models\OrderContent;
use Illuminate\Validation\ValidationException;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/resturant/{id}/categories',function($id){
    $categories = Category::where('resturant_id',$id)
    ->get(['id','resturant_id','name','price','availability','description','type','discount','path']);
    return response()->json($categories);
});

Route::get('/test', function (Request $request) {
    try {
        $validated = $request->validate([
            'resturant_id' => 'required|integer|exists:resturants,id',
            'table_number' => 'required|integer|min:1',
            'order_contents' => 'required', 
        ],[
            'resturantId.required' => 'Restaurant ID is required!',
            'resturantId.integer' => 'Restaurant ID should be a number!',
            'resturantId.exists' => 'Restaurant ID does not match with restaurants table records!',
            'tableNumber.required' => 'Table number is required!',
            'tableNumber.integer' => 'Table number should be a number!',
            'tableNumber.min' => 'Table number should be at least 1!',
            'orderContents.required' => 'Order contents are required!',
        ]);
    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    }

    $resturant_service_type = Resturant::where('id', $request->resturant_id)->get(['service_type']);
    if (in_array($resturant_service_type , ['monthly', 'just_menu_monthly'])) {
        AddOrderMonthly::dispatch($request->resturant_id,$request->table_number, $request->order_contents);
    } else {
        AddOrder::dispatch($request->resturant_id, $request->table_number, $request->order_contents);
    }

    return response()->json([
        'message' => 'order sent successfuly' ,
    ]);
});

Route::post('/resturant/order', function(Request $request) {
    try {
        $validated = $request->validate([
            'resturant_id' => 'required|integer|exists:resturants,id',
            'table_number' => 'required|integer|min:1',
            'order_contents' => 'required', 
        ],[
            'resturantId.required' => 'Restaurant ID is required!',
            'resturantId.integer' => 'Restaurant ID should be a number!',
            'resturantId.exists' => 'Restaurant ID does not match with restaurants table records!',
            'tableNumber.required' => 'Table number is required!',
            'tableNumber.integer' => 'Table number should be a number!',
            'tableNumber.min' => 'Table number should be at least 1!',
            'orderContents.required' => 'Order contents are required!',
        ]);
    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    }

    $resturant_service_type = Resturant::where('id', $request->resturant_id)->get(['service_type']);
    if (in_array($resturant_service_type , ['monthly', 'just_menu_monthly'])) {
        AddOrderMonthly::dispatch($request->resturant_id,$request->table_number, $request->order_contents);
    } else {
        AddOrder::dispatch($request->resturant_id, $request->table_number, $request->order_contents);
    }

    return response()->json([
        'message' => 'order sent successfuly' ,
    ]);
});


<?php

use App\Models\Resturant;
use App\Models\Category;
use Illuminate\Support\Facades\Route;



Route::get('/test', function () {
    $file = 'images/QRcode/test.txt';
    file_put_contents(public_path($file), 'Test write permissions');
    echo 'File written successfully';
});
Route::get('/service-worker.js', function () {
    return response()->file(public_path('service-worker.js'));
});

Route::get('/admin/{vue_capture?}', function () {
    return view('index');
})->where('vue_capture', '[\/\w\.-]*');

Route::get('/seless/menu/{resturantName}/{tableNumber}', function ($resturantName, $tableNumber) {
    $resturant = Resturant::where('name', $resturantName)->first();
    $food_response = $resturant->Food;
    $food = [];
    foreach ($food_response as $fo) {
        $food += [$fo->id => $fo];
    }

    $categories_response = Category::where('resturant_id', $resturant->id)->get(['id', 'name']);
    $categories = [];
    foreach ($categories_response as $cat) {
        $categories += [$cat->id => $cat->name];
    }

    return view('layouts.' . $resturant->tamplate_number . '.menu', compact('resturant', 'categories', 'food', 'tableNumber'));
})->middleware('run');


// Route::get('/{vue_capture?}', function() {
//     $resturant = Auth::user()->resturant;
//     if(in_array($resturant->service_type , ['per_order' , 'monthly' , 'one_year'])){
//         $order = 'true' ;
//         $saved_orders = $resturant->service_type == 'per_order'?  'true':'false';
//     }else{
//         $order = 'false';
//     }
//     return view('index' ,[
//         'id' => $resturant->id,
//         'service_type' => $resturant->service_type,
//         'order' => $order,
//         'name' => $resturant->name,
//         'saved_orders' => $saved_orders
//     ]);
// })->where('vue_capture', '[\/\w\.-]*');

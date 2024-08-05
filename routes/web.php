<?php

use App\Models\Resturant;
use App\Models\Category;
use App\Models\Food;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->middleware('auth');


Route::get('/menu/{resturantName}', function ($resturantName) {
    $resturant_info = Resturant::where('name' ,$resturantName)->first();
    // $categories_api = env('APP_URL') . ':'.'8000' . '/api/resturant/'. $resturant_info->id . '/categories';
    if($resturant_info->status == "run"){

        $food = $resturant_info->Food;
        // ->get(['id','resturant_id','name','price','availability','description','type','discount','path']);
        $categories = Category::where('resturant_id',$resturant_info->id)->get(['']);

        return view('layouts.'.$resturant_info->tamplate_number.'.menu',compact('resturant_info' , 'categories','food' ));
    }else{
        return "404 , the web is off";
    }
});

Auth::routes();




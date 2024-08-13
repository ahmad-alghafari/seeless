<?php

use App\Models\Resturant;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/{vue_capture?}', function() {
    $id = Auth::user()->resturant->id;
    return view('dashboard.index',compact('id'));
})->where('vue_capture', '[\/\w\.-]*')->middleware('auth');

Route::get('/menu/{resturantName}/{tableNumber}', function ($resturantName , $tableNumber) {
    $resturant_info = Resturant::where('name' ,$resturantName)->first();
    if($resturant_info && $resturant_info->status == "run" ){

        $food_response = $resturant_info->Food;
        $food = [] ;
        foreach($food_response as $fo){
            $food += [$fo->id => $fo];
        }

        $categories_response = Category::where('resturant_id',$resturant_info->id)->get(['id','name']);
        $categories = [];
        foreach($categories_response as $cat){
            $categories += [$cat->id => $cat->name]; 
        }

        return view('layouts.'.$resturant_info->tamplate_number.'.menu',compact('resturant_info' , 'categories','food' , 'tableNumber'));
    }else{
        return "404 , the web is off";
    }
});

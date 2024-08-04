<?php

use App\Models\Resturant;
use Illuminate\Support\Facades\Route;

Route::get('/s', function () {
    return view('welcome');
});




Route::get('/menu/{resturantName}', function ($resturantName) {
    $resturant_info = Resturant::where('name' ,$resturantName)->first();
    // dd($resturant_info);
    if($resturant_info->status == "run"){
        return view('layouts.'.$resturant_info->tamplate_number.'.menu',compact('resturant_info'));
    }else{
        return "404 , the web is off";
    }
});

Auth::routes();




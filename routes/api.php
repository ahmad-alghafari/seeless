<?php

use App\Jobs\AddOrder;
use App\Jobs\AddOrderMonthly;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Resturant;
use Illuminate\Auth\Events\Validated;
use Illuminate\Validation\ValidationException;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("/category/edit/{id}",function(Request $request , $id){
    try{
        $request->validate([
            'name' => 'required',
        ]);
        $category = Category::find($id);
        if($category){
            $update = $category->update([
                'name' => $request->name,
            ]);
            if($update){
                return response()->json([
                    'status' => "200" ,
                ]);
            }else{
                return response()->json([
                    'status' => "500" ,
                    'message' => 'can not update',
                ]);
            }
        }
        return response()->json([
            'status' => "500" ,
            'message' => 'category not found!',
        ]);

    }catch (ValidationException $e) {
        return response()->json(['server_errors' => $e->errors(), 'status' => "422"]);
    } catch (\Exception $e) {
        // Log the exception for debugging
        return response()->json([
            'status' => '500',
            'message' => 'Server error: ' . $e->getMessage(),
        ]);
    }

});

Route::delete("/food/delete/{id}" , function($id){
    try{
        $food = Food::where('id' , $id)->first();
        $oldPath = public_path($food->path); 
        if (file_exists($oldPath)) {
            unlink($oldPath); 
        }
        $food->delete; 
        return response()->json([
            'status' => '200',
            'message' => 'deleted.',
        ]);
    }catch (ValidationException $e) {
        return response()->json(['server_errors' => $e->errors(), 'status' => "422"]);
    } catch (\Exception $e) {
        // Log the exception for debugging
        return response()->json([
            'status' => '500',
            'message' => 'Server error: ' . $e->getMessage(),
        ]);
    }

});

Route::post("/food/add/{id}", function(Request $request , $id) {
    try {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '.' . '' . '.' . $file->extension();
            $filePath = "images/food/" . $fileName;
            $file->move(public_path('images/food'), $fileName);
        }else{
            return response()->json([
                'status' => '422',
                'message' => "can read file or not exist",
            ]);
        }
        $resturant = Resturant::where('id' , $id)->first();
        if($resturant){
            $food = Food::create([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'path' => $filePath ,
                'resturant_id' => $id,
                'availability' => 'availble',
            ]);
            
            if($food) {
                return response()->json([
                    'status' => '200',
                    'food' => $food
                ]);
            } else {
                return response()->json([
                    'status' => '500',
                    'message' => "Database error: Could not add the food item.",
                ]);
            }
        }else{
            return response()->json([
                'status' => '500',
                'message' => "Database error: no resturant has this id.",
            ]);
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['server_errors' => $e->errors(), 'status' => "422"]);
    } catch (\Exception $e) {
        // Log the exception for debugging
        return response()->json([
            'status' => '500',
            'message' => 'Server error: ' . $e->getMessage(),
        ]);
    }
});

Route::post("/food/change", function(Request $request) {
    try {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'category_id' => 'required',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);

            $file = $request->file('file');
            $fileName = time() . '.' . $request->id . '.' . $file->extension();
            $filePath = "images/food/" . $fileName;
            $file->move(public_path('images/food'), $fileName);
        }

        $food = Food::find($request->id);
        if (!$food) {
            return response()->json([
                'status' => '404',
                'message' => 'Food item not found.',
            ]);
        }
        if($filePath){
            $oldPath = public_path($food->path); 
            if (file_exists($oldPath)) {
                unlink($oldPath); 
            }
        }
        

        // $discount = $request->discount !== null && $request->discount !== '' ? $request->discount : NULL;

        $updated = $food->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
            // 'discount' => $discount,
            'path' => $filePath ?: $food->path, // Keep existing path if no file is uploaded
        ]);

        if ($updated) {
            return response()->json([
                'path' => $filePath ?: $food->path, // Send the correct path
                'updated_at' => $food->updated_at,
                'status' => '200',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'message' => "Database error: Could not update the food item.",
            ]);
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['server_errors' => $e->errors(), 'status' => "422"]);
    } catch (\Exception $e) {
        // Log the exception for debugging
        return response()->json([
            'status' => '500',
            'message' => 'Server error: ' . $e->getMessage(),
        ]);
    }
});
Route::post("/food/status",function(Request $request){
    $food = Food::find($request->id);
    try{
        $food->update([
            'availability' => $request->status
        ]);
        return response()->json([
            "status" => "200" ,
        ]);
    }catch(ValidationException $e){
        return response()->json(['server : errors ->' => $e->errors() , 'status' => 422], 422);
    } catch (\Exception $e) {
        // Log the exception for debugging
        return response()->json([
            'status' => '500',
            'message' => 'Server error: ' . $e->getMessage(),
        ]);
    }
    
});

Route::get("/foodAndCategories/{id}",function($id){
    try{
        $food_response = Food::where("resturant_id" , $id)->get();
        $categories_response = Category::where("resturant_id" , $id)->get();

        $food = [] ;
        foreach($food_response as $fo){
            $food += [$fo->id => $fo];
        }

        $categories = [];
        foreach($categories_response as $cat){
            $categories += [$cat->id => $cat->name]; 
        }

        return response()->json([
            'status' => '200' ,
            'food' => $food ,
            'categories' => $categories ,
        ]);
    }catch(ValidationException $e){
        return response()->json(['server : errors ->' => $e->errors()], 422);
    }
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
        return response()->json(['server : errors ->' => $e->errors()], 422);
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
<?php

use App\Jobs\AddOrder;
use App\Jobs\AddOrderMonthly;
use App\Models\Category;
use App\Models\MonthlyOrder;
use App\Models\Order;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Resturant;
use Illuminate\Validation\ValidationException;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/orders/add', function(Request $request) {
    try {
        $request->validate([
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

    $resturant = Resturant::where('id', $request->resturant_id)->first();
    if ($resturant->service_type == 'per_order') {
        AddOrderMonthly::dispatch($request->resturant_id,$request->table_number, $request->order_contents);
    } else {
        AddOrder::dispatch($request->resturant_id, $request->table_number, $request->order_contents);
    }

    return response()->json([
        'status' => '200',
    ]);
});

Route::get("/orders/{id}" , function($id){
    try{
        $resturant = Resturant::find($id);
        if($resturant){
            if($resturant->service_type == "per_order"){
                $temp_orders = 
                $resturant
                ->MonthlyOrder()
                ->select('id', 'table_number', 'created_at')
                ->with(['Content' => function ($query) {
                    $query->select('id', 'food_id', 'count' , 'order_id'); 
                }])
                ->where('implemented', 'false')
                ->orderBy('created_at', 'desc')
                ->get();
    
                $orders = [];
                foreach($temp_orders as $order){
                    $orders[$order->id] = $order;
                }   
            }else{
                $temp_orders = $resturant->Order()->with('Content')->get();
                $orders = [];
                foreach($temp_orders as $order){
                    $orders[$order->id] = $order;
                } 
            }
            if($orders){
                $food = [] ;
                $temp_food = $resturant->Food()->select('id', 'name', 'price', 'discount')->get();
                foreach($temp_food as $fo){
                    $food += [$fo->id => $fo];
                }
                return response()->json([
                    'status' => '200' ,
                    'orders' => $orders ,
                    'food' => $food
                ]);
            }else{
                return response()->json([
                    'status' => '404' ,
                    'message' => 'no orders found' 

                ]);
            }
        }
        return response()->json([
            'status' => '404' ,
            'message' => 'no resturant found' 
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

Route::get("/orders/implemented/{id}" , function($id){
    try{
        $resturant = Resturant::find($id);
        if($resturant){
            if($resturant->service_type == "per_order"){
                $temp_orders = 
                $resturant
                ->MonthlyOrder()
                ->select('id', 'table_number', 'created_at')
                ->with(['Content' => function ($query) {
                    $query->select('id', 'food_id', 'count' , 'order_id'); 
                }])
                ->where('implemented', 'true')
                ->orderBy('created_at', 'desc')
                ->get();
    
                $orders = [];
                foreach($temp_orders as $order){
                    $orders[$order->id] = $order;
                }   
            }else{
                $temp_orders = $resturant->Orders()->with('Content')->get();
                $orders = [];
                foreach($temp_orders as $order){
                    $orders[$order->id] = $order;
                } 
            }
            if($orders){
                $food = [] ;
                $temp_food = $resturant->Food()->select('id', 'name', 'price', 'discount')->get();
                foreach($temp_food as $fo){
                    $food += [$fo->id => $fo];
                }
                return response()->json([
                    'status' => '200' ,
                    'orders' => $orders ,
                    'food' => $food , 
                    'service_price' => $resturant->service_price ,
                ]);
            }else{
                return response()->json([
                    'status' => '404' ,
                    'message' => 'no orders found' 

                ]);
            }
        }
        return response()->json([
            'status' => '404' ,
            'message' => 'no resturant found' 
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


Route::post("/orders/implement",function(Request $request){
    try{
        $resturant = Resturant::find($request->id);

        if (!$resturant) {
            return response()->json([
                'status' => '404',
                'message' => 'Restaurant not found',
            ]);
        }

        
        $query = null;
        if($resturant->service_type == "per_order"){
            $query = $resturant
            ->MonthlyOrder()
            ->where('id', $request->order_id)
            ->update(['implemented' => 'true']);
        }else{
            $query = $resturant
            ->Order()
            ->where('id', $request->order_id)
            ->delete();
        }

        if ($query) {
            return response()->json([
                'status' => '200',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'message' => 'Unable to update the order implementation status',
            ], 500);
        }
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

// Route::delete("/orders/delete/{id}" , function(Request $request , $id){
// });

// ---------- category --------------

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

Route::delete("category/delete/{id}" , function($id){
    try{
        $deleted = Category::destroy($id);
        if($deleted){
            return response()->json([
                'status' => "200" ,
            ]);
        }
        return response()->json([
            'status' => "500" ,
            'message' => 'db : can not delete category',
        ]);
    }catch (ValidationException $e) {
        return response()->json(['message' => $e->errors(), 'status' => "422"]);
    } catch (\Exception $e) {
        // Log the exception for debugging
        return response()->json([
            'status' => '500',
            'message' => 'Server error: ' . $e->getMessage(),
        ]);
    }
});

Route::post("/category/create/{id}", function(Request $request , $id) {
    try {
        $request->validate([
            'name' => 'required',
        ]);

        
        $resturant = Resturant::where('id' , $id)->first();
        if($resturant){
            $category = $resturant->Category()->create([
                'name' => $request->name,
            ]);
            
            if($category) {
                return response()->json([
                    'status' => '200',
                    'id' => $category->id
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


// ---------- food --------------
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
            $fileName = time() . '.' . $id . '.' . $file->extension();
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
    try{
        $food = Food::find($request->id);
        $food->update([
            'availability' => $request->status
        ]);
        return response()->json([
            "status" => "200" ,
            'updated_at' => $food->updated_at
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


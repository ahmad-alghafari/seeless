<?php

use App\Jobs\AddOrder;
use App\Jobs\AddOrderMonthly;
use App\Models\Category;
use App\Models\MonthlyOrder;
use App\Models\Order;
use App\Models\Food;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\QRcodeController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Resturant;
use Illuminate\Validation\ValidationException;

Route::post('/login', [PassportController::class, 'login']);
Route::post('/register', [PassportController::class, 'register']);
Route::post('/logout', [PassportController::class, 'logout'])->middleware('auth:api');


Route::middleware("auth:api")->group(function () {
    Route::prefix("/QRcode")->group(function () {
        Route::get('/', [QRcodeController::class, 'index']);
        Route::post('/generate', [QRcodeController::class, 'generate']);
        Route::delete('/delete/{id}', [QRcodeController::class, 'destroy']);
    });
    Route::get('/check-token', function () {
        return response()->json([
            'valid' => true,
            'message' => 'Token is valid',
        ], 200);
    });

    Route::prefix("/orders")->group(function () {
        Route::get("/", function () {
            try {
                $resturant = auth()->user()->resturant;
                if (!$resturant) {
                    return response()->json([
                        'message' => 'No rest found '
                    ], 404);
                }
                if ($resturant->service_type == "per_order") {
                    $temp_orders =
                        $resturant
                        ->MonthlyOrder()
                        ->select('id', 'table_number', 'created_at')
                        ->with(['Content' => function ($query) {
                            $query->select('id', 'food_id', 'count', 'order_id');
                        }])
                        ->where('implemented', 'false')
                        ->orderBy('created_at', 'desc')
                        ->get();

                    $orders = [];
                    foreach ($temp_orders as $order) {
                        $orders[$order->id] = $order;
                    }
                } else {
                    $temp_orders = $resturant->Order()->with('Content')->get();
                    $orders = [];
                    foreach ($temp_orders as $order) {
                        $orders[$order->id] = $order;
                    }
                }
                if ($orders) {
                    $food = [];
                    $temp_food = $resturant->Food()->select('id', 'name', 'price', 'discount')->get();
                    foreach ($temp_food as $fo) {
                        $food += [$fo->id => $fo];
                    }
                    return response()->json([
                        'orders' => $orders,
                        'food' => $food
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'no orders found'
                    ], 404);
                }
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });



        Route::get("/implemented", function () {
            try {
                $resturant = auth()->user()->resturant;
                if ($resturant) {
                    if ($resturant->service_type == "per_order") {
                        $temp_orders =
                            $resturant
                            ->MonthlyOrder()
                            ->select('id', 'table_number', 'created_at')
                            ->with(['Content' => function ($query) {
                                $query->select('id', 'food_id', 'count', 'order_id');
                            }])
                            ->where('implemented', 'true')
                            ->orderBy('created_at', 'desc')
                            ->get();

                        $orders = [];
                        foreach ($temp_orders as $order) {
                            $orders[$order->id] = $order;
                        }
                    } else {
                        $temp_orders = $resturant->Orders()->with('Content')->get();
                        $orders = [];
                        foreach ($temp_orders as $order) {
                            $orders[$order->id] = $order;
                        }
                    }
                    if ($orders) {
                        $food = [];
                        $temp_food = $resturant->Food()->select('id', 'name', 'price', 'discount')->get();
                        foreach ($temp_food as $fo) {
                            $food += [$fo->id => $fo];
                        }
                        return response()->json([
                            'orders' => $orders,
                            'food' => $food,
                            'service_price' => $resturant->service_price,
                        ], 200);
                    } else {
                        return response()->json([
                            'message' => 'no orders found'
                        ], 404);
                    }
                }
                return response()->json([
                    'message' => 'no resturant found'
                ], 404);
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });


        Route::post("/implement", function (Request $request) {
            try {
                $resturant = auth()->user()->resturant;

                if (!$resturant) {
                    return response()->json([
                        'message' => 'Restaurant not found',
                    ], 404);
                }


                $query = null;
                if ($resturant->service_type == "per_order") {
                    $query = $resturant
                        ->MonthlyOrder()
                        ->where('id', $request->order_id)
                        ->update(['implemented' => 'true']);
                } else {
                    $query = $resturant
                        ->Order()
                        ->where('id', $request->order_id)
                        ->delete();
                }

                if ($query) {
                    return response()->json([
                        'message' => 'implemented succes',
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Unable to update the order implementation status',
                    ], 500);
                }
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });
    });

    Route::prefix("/category")->group(function () {
        Route::get("/", function () {
            try {
                $categories_response = auth()->user()->resturant->Category;
                $categories = [];
                foreach ($categories_response as $cat) {
                    $categories += [$cat->id => ["name" => $cat->name, "updated_at" => $cat->updated_at]];
                }
                return response()->json([
                    'categories' => $categories,
                ], 200);
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });

        Route::post("/edit", function (Request $request) {
            try {
                $request->validate([
                    'name' => 'required',
                ]);
                $category = Category::find($request->id);
                if ($category) {
                    $update = $category->update([
                        'name' => $request->name,
                    ]);
                    if ($update) {
                        return response()->json([
                            'message' => "updated success",
                        ], 200);
                    } else {
                        return response()->json([
                            'message' => 'db can not update',
                        ], 500);
                    }
                }
                return response()->json([
                    'message' => 'category not found!',
                ], 404);
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });

        Route::delete("/delete/{id}", function ($id) {
            try {
                $deleted = Category::destroy($id);
                if ($deleted) {
                    return response()->json([
                        'message' => "deleted success",
                    ], 200);
                }
                return response()->json([
                    'message' => 'can not fount category',
                ], 404);
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });

        Route::post("/create", function (Request $request) {
            try {
                $request->validate([
                    'name' => 'required',
                ]);


                $resturant = auth()->user()->resturant;
                if ($resturant) {
                    $category = $resturant->Category()->create([
                        'name' => $request->name,
                    ]);

                    if ($category) {
                        return response()->json([
                            'id' => $category->id,
                        ], 200);
                    } else {
                        return response()->json([
                            'message' => "Database error: Could not add the food item.",
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'message' => "Database error: no resturant has this id.",
                    ], 500);
                }
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });
    });

    Route::prefix("/food")->group(function () {
        Route::get("/", function () {
            try {
                $food_response = auth()->user()->resturant->Food;
                $categories_response = auth()->user()->resturant->Category;

                $food = [];
                foreach ($food_response as $fo) {
                    $food += [$fo->id => $fo];
                }

                $categories = [];
                foreach ($categories_response as $cat) {
                    $categories += [$cat->id => $cat->name];
                }

                return response()->json([
                    'food' => $food,
                    'categories' => $categories,
                ], 200);
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });

        Route::delete("/delete", function (Request $request) {
            try {
                $food = Food::find($request->id);
                $oldPath = public_path($food->path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
                $food->delete;
                return response()->json([
                    'message' => 'deleted success',
                ], 200);
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });

        Route::post("/add", function (Request $request) {
            try {
                $request->validate([
                    'name' => 'required',
                    'price' => 'required',
                    'description' => 'required',
                    'category_id' => 'required|exists:categories,id',
                    'file' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
                ]);

                if ($request->hasFile('file')) {
                    $resturant = auth()->user()->resturant;

                    $file = $request->file('file');
                    $fileName = time() . '.' . $resturant->id . '.' . $file->extension();
                    $filePath = "images/food/" . $fileName;
                    $file->move(public_path('images/food'), $fileName);

                    $resturant->Food()->create([
                        'name' => $request->name,
                        'price' => $request->price,
                        'description' => $request->description,
                        'category_id' => $request->category_id,
                        'path' => $filePath,
                        'availability' => 'availble',
                    ]);

                    return response()->json([
                        'message' => "dishes added succesfilly"
                    ], 200);
                } else {
                    return response()->json([
                        'message' => "can read file or not exist",
                    ], 422);
                }
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });

        Route::post("/change", function (Request $request) {
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
                        'message' => 'Food item not found.',
                    ], 404);
                }
                if ($filePath) {
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
                    'path' => $filePath ?: $food->path, // Keep existing path if no file is uploaded
                ]);

                if ($updated) {
                    return response()->json([
                        'path' => $filePath ?: $food->path, // Send the correct path
                        'updated_at' => $food->updated_at,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => "Database error: Could not update the food item.",
                    ], 500);
                }
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });

        Route::post("/status", function (Request $request) {
            try {
                $food = Food::find($request->id);
                $food->update([
                    'availability' => $request->status
                ]);
                return response()->json([
                    'updated_at' => $food->updated_at
                ], 200);
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });

        Route::get("/item", function (Request $request) {
            try {
                $food = Food::where('id', $request->id)->with("Category:id,name")->first();
                if ($food) {
                    $categories_response = auth()->user()->resturant->Category;

                    $categories = [];
                    foreach ($categories_response as $cat) {
                        $categories += [$cat->id => $cat->name];
                    }

                    return response()->json([
                        'food' => $food,
                        'categories' => $categories
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'not found',
                    ], 404);
                }
            } catch (ValidationException $e) {
                return response()->json(['server_errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                // Log the exception for debugging
                return response()->json([
                    'message' => 'Server error: ' . $e->getMessage(),
                ], 500);
            }
        });
    });
});


Route::post('/orders/add', function (Request $request) {
    try {
        $request->validate([
            'resturant_id' => 'required|integer|exists:resturants,id',
            'table_number' => 'required|integer|min:1',
            'order_contents' => 'required',
        ], [
            'resturantId.required' => 'Restaurant ID is required!',
            'resturantId.integer' => 'Restaurant ID should be a number!',
            'resturantId.exists' => 'Restaurant ID does not match with restaurants table records!',
            'tableNumber.required' => 'Table number is required!',
            'tableNumber.integer' => 'Table number should be a number!',
            'tableNumber.min' => 'Table number should be at least 1!',
            'orderContents.required' => 'Order contents are required!',
        ]);
        $resturant = Resturant::where('id', $request->resturant_id)->first();
        if ($resturant->service_type == 'per_order') {
            AddOrderMonthly::dispatch($request->resturant_id, $request->table_number, $request->order_contents);
        } else {
            AddOrder::dispatch($request->resturant_id, $request->table_number, $request->order_contents);
        }

        return response()->json([
            'message' => 'added success',
        ], 200);
    } catch (ValidationException $e) {
        return response()->json(['server_errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        // Log the exception for debugging
        return response()->json([
            'message' => 'Server error: ' . $e->getMessage(),
        ], 500);
    }
});

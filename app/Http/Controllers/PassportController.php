<?php
 
namespace App\Http\Controllers;
 
use App\Models\User;
use Illuminate\Http\Request;
 
class PassportController extends Controller
{
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => "1",
            'password' => bcrypt($request->password) ,
        ]);
 
        $token = $user->createToken('Seless')->accessToken;
 


      
   
        return response()->json([
            'token' => $token ,
            "status" => "200" ,
        ]);
    }
 
    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('Seless')->accessToken;
            $resturant = auth()->user()->resturant;
            if(in_array($resturant->service_type , ['per_order' , 'monthly' , 'one_year'])){
                $order = 'true' ;
                $saved_orders = $resturant->service_type == 'per_order'?  'true':'false';
            }else{
                $order = 'false';
            }
            return response()->json([
                'token' => $token ,
                "status" => "200" ,
                'resturant_id' => $resturant->id ,
                'name' => $resturant->name,
                'saved_orders' => $saved_orders,
                'order' => $order,
                'service_type' => $resturant->service_type,
                'id' => $resturant->id,
            ]);
        } else {
            return response()->json(['error' => 'UnAuthorised' , "status" => "401"]);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();

        $token->revoke();

        return response()->json([
            'message' => 'Successfully logged out',
            'status' => "200"
        ]);
    }
 
    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function details()
    {
        return response()->json(['user' => auth()->user()], 200);
    }
}
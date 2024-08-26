<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        // data validation
        $request->validate([
            "name" => "required",
            "phone" => "required|unique:users",
            "password" => "required|confirmed",
            'role' => 'required|in:pharmacy,warehouse',
        ]);
        // User Model
        $role1 = $request->role;

        if($role1 == "pharmacy")
        {
            $val = $request->validate([
                "pharmacy_name" => "required",
                "pharmacy_location" => "required",
            ]);

            if (!$val) {
                return response()->json([
                    "status" => false,
                    "message" => "Required data missing",
                ]);
            }

            $user1 = User::create([
                "name" => $request->name,
                "phone" => $request->phone,
                'role' => $request->role,
                "fcm_token" =>$request->fcm_token,
                "password" => Hash::make($request->password)
            ]);

            $pharm =Pharmacy::create([
                "id" => $user1->id,
                "pharmacy_name" => $request->pharmacy_name,
                "pharmacy_location" => $request->pharmacy_location,
                "user_id"=>$user1->id,
            ]);
            return response()->json([
                "status" => true,
                "message" => "User registered successfully at $pharm->pharmacy_name "
            ]);
        }

        if($role1 == "warehouse")
        {
            $val = $request->validate([
                "warehouse_name" => "required",
                "location" => "required",
            ]);

            if (!$val) {
                return response()->json([
                    "status" => false,
                    "message" => "Required data missing",
                ]);
            }

            $user1 = User::create([
                "name" => $request->name,
                "phone" => $request->phone,
                'role' => $request->role,
                "fcm_token" =>$request->fcm_token,
                "password" => Hash::make($request->password)
            ]);

            $ware =Warehouse::create([
                "id" => $user1->id ,
                "warehouse_name" => $request->warehouse_name,
                "location" => $request->location,
                "user_id"=>$user1->id,
            ]);
            return response()->json([
                "status" => true,
                "message" => "User registered successfully at $ware->warehouse_name"
            ]);
        }
        // Response
        // Response
        return response()->json([
            "status" => true,
            "message" => "missing data"
        ]);
    }
    public function login(Request $request)
    {

        // data validation
        $request->validate([
            "phone" => "required",
            "password" => "required"
        ]);

        // JWTAuth
        $token = JWTAuth::attempt([
            "phone" => $request->phone,
            "password" => $request->password
        ]);

        if (!empty($token)) {

            return response()->json([
                "status" => true,
                "message" => "User logged in succcessfully",
                "token" => $token
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Invalid details"
        ]);
    }
    public function profile()
    {

        $userdata = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $userdata
        ]);
    }
    public function logout()
    {

        auth()->logout();
        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }
}

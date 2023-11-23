<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    // User Register (POST, formdata)
    public function register(Request $request){

        // data validation
        $request->validate([
            "name" => "required",
            "phone" => "required|unique:users",
            "password" => "required|confirmed"
        ]);

        // User Model
        User::create([
            "name" => $request->name,
            "phone" => $request->phone,
            "password" => Hash::make($request->password)
        ]);

        // Response
        return response()->json([
            "status" => true,
            "message" => "User registered successfully"
        ]);
    }

    // User Login (POST, formdata)
    public function login(Request $request){

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

        if(!empty($token)){

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
    public function refreshToken(){

        $newToken = auth()->refresh();

        return response()->json([
            "stat us" =>true ,
            "message" => "new access token generated" ,
            "token" => $newToken
        ]);

    }
    // User Profile (GET)
    public function profile(){

        $userdata = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $userdata
        ]);
    }

    // To generate refresh token value
    // public function refreshToken(){

    //     $newToken = auth()->refresh();

    //     return response()->json([
    //         "status" => true,
    //         "message" => "New access token",
    //         "token" => $newToken
    //     ]);
    // }

    // User Logout (GET)
    public function logout(){

        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }
}

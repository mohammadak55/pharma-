<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\MedicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);

Route::group([
    "middleware" => ["auth:api"]
], function(){

    Route::get("profile", [ApiController::class, "profile"]);
    Route::get("refresh", [ApiController::class, "refreshToken"]);
    Route::get("logout", [ApiController::class, "logout"]);
});


Route::get("Get_AllMedications_ByCategories" , [MedicationController::class , "getAllMedicationsByCategory"]);
Route::post("Get_Medication_ByItsCategory" , [MedicationController::class , "searchMedicationByCategory"]);
Route::post("Get_Medication_ByItsName" , [MedicationController::class , "searchMedicationByName"]);


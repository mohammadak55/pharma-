<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\DepotController;
use App\Http\Controllers\Api\MedicationController;
use App\Http\Controllers\DepotController as ControllersDepotController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::get("/", function () {
    Storage::disk("public")->put("test.txt", 'welcome');
    return "ok";
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// المستودع
Route::post("register_warehouse", [ApiController::class, "register"]);
Route::post("login_warehouse", [ApiController::class, "login"]);
Route::group(['middleware' => ['auth.jwt', 'check.role:warehouse']], function ()
    {
        Route::get("profileWareHouse", [ApiController::class, "profile"]);
        Route::post("refreshWareHouse", [NotificationController::class, "refreshToken"]);
        Route::get("logoutWareHouse", [ApiController::class, "logout"]);
        // insert medications
        Route::post("insert_Medication", [WarehouseController::class, "AddMedication"]);
        Route::post("insert_categories", [WarehouseController::class, "AddCategory"]);
        Route::get("Get_AllMedications_ByCategoriesInWarehouse", [WarehouseController::class, "getAllMedicationsByCategory"]);
        Route::post("Search_Medications_ByCategoriesInWarehouse", [WarehouseController::class, "searchMedicationByCategory"]);
        Route::post("Search_Medications_ByNameInWarehouse", [WarehouseController::class, "searchMedicationByName"]);
        Route::post("ChangeOrderStatus", [WarehouseController::class, "changeStatus"]);
        Route::get("ShowOrderInWareHouse", [WarehouseController::class, "ShowOrder_inWareHouse"]);
        Route::post('/notificationsWarehouse', [NotificationController::class, 'sendNotification']);
    }
);
//الصيدلي
Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);
Route::group(['middleware' => ['auth.jwt', 'check.role:pharmacy']], function ()
    {
        Route::get("profile", [ApiController::class, "profile"]);
        Route::post("refresh", [NotificationController::class, "refreshToken"]);
        Route::post('/notifications', [NotificationController::class, 'sendNotification']);
        Route::get("logout", [ApiController::class, "logout"]);
        Route::get("Get_AllMedications_ByCategories", [MedicationController::class, "getAllMedicationsByCategory"]);
        Route::post("Get_Medication_ByItsCategory", [MedicationController::class, "searchMedicationByCategory"]);
        Route::post("Get_Medication_ByItsName", [MedicationController::class, "searchMedicationByName"]);
        Route::post("RequestOrder", [MedicationController::class, "request_order"]);
        Route::get("Show_Order", [MedicationController::class, "ShowOrder"]);
        Route::post("insert_fav" , [FavoriteController::class , "InsertIntoFav"]);
        Route::post("delete_fav" , [FavoriteController::class , "DelteFromFav"]);
        Route::get("show_fav" , [FavoriteController::class , "ShowFav"]);
        Route::delete("delete_favs" , [FavoriteController::class , "emptyFav"]);


    }
);





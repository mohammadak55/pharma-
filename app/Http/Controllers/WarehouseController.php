<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Medication;
use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WarehouseController extends Controller
{

    public function AddMedication(Request $request )
    {
        $request->validate([
            "Trade_Name" => "required",
            "scientific_name" => "required",
            "Manufacturer" => "required",
            "image" => "required",
            "Available_quantity" => "required",
            "Expiry_date" => "required",
            "Price" => "required",
            "category_id" => "required"
        ]);

            $requestData = $request->all();

            $existingRecord = Medication::where('Trade Name', $requestData['Trade_Name'])->first();

            if ($existingRecord) {
                if($existingRecord->warehouse_id ==  auth()->id()) {
                // Update existing record by adding the new quantity to the existing quantity
                $existingRecord->update([
                    'Available quantity' => $existingRecord->{'Available quantity'} + $requestData['Available_quantity'],
                ]);
                $existingRecord->update([
                    'Price' => $requestData['Price'],
                ]);
            }}
             else {
                $request->validate([
                    "Trade_Name" => "required",
                    "scientific_name" => "required",
                    "Manufacturer" => "required",
                    "image" => "required",
                    "Available_quantity" => "required",
                    "Expiry_date" => "required",
                    "Price" => "required",
                    "category_id" => "required",
                ]);
                $image = $request->file("image")->getClientOriginalName();
                $path = $request->file("image")->storeAs("storage" , $image , "image");
                $image1 = $request->file('image');
                $base64Image = base64_encode(file_get_contents($image1->path()));

                Medication::create([
                    'scientific name' => $requestData['scientific_name'],
                    'Trade Name' => $requestData['Trade_Name'],
                    'Manufacturer' => $requestData['Manufacturer'],
                    'ImagePath' => $base64Image ,
                    'Available quantity' => $requestData['Available_quantity'],
                    'Expiry date' => $requestData['Expiry_date'],
                    'Price' => $requestData['Price'],
                    'category_id' => $requestData['category_id'],
                    "warehouse_id" => auth()->id(),
                ]);
            }


            return response()->json(['message' => 'medications saved successfully'] ,200 );
    }
    public function AddCategory(Request $request)
    {
        $request->validate([
            "categories" => "required|unique:categories"
        ]);
        Categories::create([
            "categories" => $request->categories
        ]);
        return response()->json(['message' => 'categories saved successfully'] ,200 );
    }
    public function getAllMedicationsByCategory()
    {
    $warehouseId = auth()->id();
    $categories = Categories::with(['medications' => function ($query) use ($warehouseId) {
        $query->where('warehouse_id', $warehouseId);
    }])
    ->get();
    return response()->json($categories);

    }
    public function searchMedicationByCategory(Request $request)
    {
        $warehouseId = auth()->id();
        $request->validate([
            'category_name' => 'required|string',
        ]);
        $categoryName = $request->input('category_name');
        $category = Categories::where('categories', $categoryName)->with(['medications' => function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }])->first();

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json([
            $category
        ]);
    }
    public function searchMedicationByName(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $name = $request->name ;
        $medications=Categories::where('id', Medication::where('Trade name', $name)->where('warehouse_id', auth()->id())->first()->category_id) ->with('medications')->get();
        $med[]=Medication::where('Trade name',$name)->first();
        $json[]=json_decode($medications,true);
        $json[0][0]['medications']=$med;

        if ($medications->isEmpty()) {
            return response()->json(['error' => 'name not found'], 404);
        }

        return response($json[0] , 200);
    }
    public function changeStatus(Request $request)
    {
        $order = Order::with('order_Item')->where('order_num', '=', $request->order_number)->first();
        $changeStatus = $request->state_num;

        if ($order) {
            try {
                switch ($changeStatus) {
                    case 1:
                        $order->update(['status' => 'completed']);
                        $order->update(['is_paid' => true]);
                        return response(["message" => "Order status updated to completed"], 200);
                        break;

                    case 2:
                        $order->update(['status' => 'processing']);
                        $orderItems = $order->order_item;

                        if ($orderItems) {
                            foreach ($orderItems as $orderItem) {
                                $medication = Medication::find($orderItem->medication_id);

                                if ($medication && $medication->{"Available quantity"} >= $orderItem->quantity) {
                                    $medication->update([
                                        "Available quantity" => $medication->{"Available quantity"} - $orderItem->quantity,
                                    ]);
                                } else {
                                    $order->update(['status' => 'decline']);
                                    return response()->json(["error" => "not-enough quantity or medication not found and Order status updated to decline"], 400);
                                }
                            }

                            return response(["message" => "Order status updated to processing"], 200);
                        } else {
                            return response(["error" => "there is no items in the order to updated"], 400);
                        }
                        break;

                    default:
                        return response(["error" => "Invalid status"], 400);
                }
            } catch (\Exception $e) {
                return response()->json(["error" => $e->getMessage()], 500);
            }
        } else {
            return response("order not found", 404);
        }
    }
    public function ShowOrder_inWareHouse(Request $request)
    {
        $orders = Order::with(['order_item' => function ($query) {
            $query->with(['medication' => function ($query) {
                $query->select('id', 'Trade Name', 'Price');
            }]);
        }])
            ->get();

        if ($orders->isNotEmpty()) {
            $formattedOrders = $orders->map(function ($order) {
                return [
                    'order_num' => $order->order_num,
                    "user_id" => $order->user_id,
                    'pharmacy_name' => Pharmacy::find($order->user_id)->pharmacy_name,
                    'pharmacy_location' => Pharmacy::find($order->user_id)->pharmacy_location,
                    'status' => $order->status,
                    'totalPrice' => $order->totalPrice,
                    'created_at' => $order->created_at,
                    'items' => $order->order_item->map(function ($orderItem) {
                        return [
                            'medication_name' => $orderItem->medication_name,
                            'price' => $orderItem->medication->Price,
                        ];
                    }),
                ];
            });

            return response($formattedOrders, 200);
        } else {
            return response("No order found", 404);
        }
    }

}


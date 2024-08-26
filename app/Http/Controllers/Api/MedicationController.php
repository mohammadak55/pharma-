<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Medication;
use App\Models\Order;
use App\Models\Order_item;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function getAllMedicationsByCategory()
    {

        $medicationsByCategory = Categories::with('medications')->get();

        return response($medicationsByCategory, 200);
    }
    public function searchMedicationByCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string',
        ]);
        $categoryName = $request->input('category_name');
        $category = Categories::where('categories', $categoryName)->with('medications')->first();
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response($category, 200);
    }
    public function searchMedicationByName(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $name = $request->name ;
        $medications=Categories::where('id', Medication::where('Trade name', $name)->first()->category_id) ->with('medications')->get();
        $med[]=Medication::where('Trade name',$name)->first();
        $json[]=json_decode($medications,true);
        $json[0][0]['medications']=$med;

        if ($medications->isEmpty()) {
            return response()->json(['error' => 'name not found'], 404);
        }

        return response($json[0] , 200);
    }
    public function ShowOrder(Request $request)
    {
        $pharm_id = auth()->id();
        $orders = Order::with(['order_item' => function ($query) {
            $query->with(['medication' => function ($query) {
                $query->select('id', 'Trade Name', 'Price');
            }]);
        }])
            ->where("user_id", $pharm_id)
            ->get();

        if ($orders->isNotEmpty()) {
            $formattedOrders = $orders->map(function ($order) {
                return [
                    'order_num' => $order->order_num,
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
    public function request_order(Request $request)
    {
        $ord = uniqid('ORD.');
        $order = Order::create([
            "order_num" => $ord,
            "user_id" => auth()->id(),
            "totalPrice" => 0,
        ]);
        $id = $order->id;
        $totalPrice = 0; // Initialize total price variable

        $items = json_decode($request->getContent(), true);

        foreach ($items as $item) {
            $orderItem = Order_item::create([
                "medication_name" =>  $item['nameOfMedicines'],
                "quantity" => $item['quantity'],
                "order_id" => $order->id,
                "medication_id" => 1,
            ]);


            $var = Medication::where('Trade Name', '=', $orderItem->medication_name)->first();
            $varid = $var->id;
            $orderItem->update([
                "medication_id" => $varid,
            ]);
            // Calculate and update total price for each item
            $totalPrice += $var->Price * $item['quantity'];
        }

        // Update total price in the order
        $order->update([
            "totalPrice" => $totalPrice,
        ]);

        return response()->json(["message" => "Order placed successfully with $ord and totalPrice :$totalPrice "]);
    }
}



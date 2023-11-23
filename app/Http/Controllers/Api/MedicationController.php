<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Medication;
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
        $medications = $category->medications;

        return response($medications , 200);
    }

    public function searchMedicationByName(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $name = $request->input('name');
        $medications = Medication::where('Trade Name' , $name)->get();
        if ($medications->isEmpty()) {
            return response()->json(['error' => 'name not found'], 404);
        }

        return response($medications , 200);
    }

}

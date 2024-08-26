<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Medication;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function InsertIntoFav(Request $request)
    {
        $request->validate([
            "name" => "required",
        ]);
        $name = $request->name;
        $fav = Favorite::where('MedicinName', $name)->first();
        if(!$fav){
        $medication =  Medication::where('Trade name', $name)->first();
        if ($medication )
        {
            $med_id = $medication->id;
            $fav = Favorite::create(
                [
                    "MedicinName" => $name ,
                    "med_id" => $med_id,
                ]
                );
            return response()->json([
                "message" => "$name added to favorite's list successfuly ... "
            ]);
        }
        else
        return response()->json([
        "message" => "medicin not found "
        ]);
        }
        return response()->json([
            "message" => "medicin is allready in fav list "
            ]);
    }
    public function DelteFromFav(Request $request)
    {
        $request->validate([
            "id" => "required",
        ]);
        $id = $request->id;
        $fav = Favorite::where("id" , $id)->first();
        $favname = $fav->MedicinName;
        if ($fav){
            $fav->delete();
            return response("$favname deleted successfuly from favorite's list ");
        }
        else
            return response("there is an error ... or $favname is allready out the favorite's list ");
    }
    public function ShowFav()
    {
        $medication_favorits = Favorite::get();
        if($medication_favorits){
            return response($medication_favorits);
        }
        else
        return response()->json([
            "message"=>"favorites list empty"
        ]);

    }
    public function emptyFav(Request $request)
    {
        $fav = Favorite::all();

        if ($fav->isNotEmpty()) {
            Favorite::truncate();

            return response()->json([
                "message" => "Favorites list truncated successfully."
            ]);
        } else {
            return response()->json([
                "message" => "Favorites list is already empty."
            ]);
        }
    }
}

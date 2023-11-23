<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;


    public function medications()
    {
        return $this->hasMany(Medication::class , "category_id");
    }
}

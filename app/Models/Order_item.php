<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_item extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

}

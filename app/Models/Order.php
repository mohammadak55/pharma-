<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function order_item()
    {
        return $this->hasMany(Order_item::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

}

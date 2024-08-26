<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Medication extends Model
{
    use HasFactory;
    use Notifiable;
    protected $guarded=[];

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }
    public function favorite()
    {
        return $this->belongsTo(Favorite::class);
    }

    public function orderItems()
    {
        return $this->hasMany(Order_Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}

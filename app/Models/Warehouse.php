<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $guarded=[];

public function medication()
{
    return $this->hasMany(Medication::class);
}
public function user()
{
    return $this->hasMany(User::class);
}

}

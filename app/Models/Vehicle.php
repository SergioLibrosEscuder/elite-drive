<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicles';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        "hourly_price",
        "manufacturing_year",
        "traction",
        "transmission",
        "engine",
        "engine_capacity",
        "description",
        "doors",
        "license_plate",
        "brand",
        "model",
        "fuel_type",
        "color",
        "status",

    ];
}

<?php
// Sergio Libros

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    // Define the table associated with the model
    protected $table = 'vehicles';
    // Define the primary key of the table
    protected $primaryKey = 'id';
    // Enable timestamps for created_at and updated_at fields
    public $timestamps = true;
    // Define fillable fields for mass assignment
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

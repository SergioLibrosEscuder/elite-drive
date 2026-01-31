<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();
        return response()->json($vehicles);
    }

    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        if ($vehicle) {
            return response()->json($vehicle);
        }
    }
}

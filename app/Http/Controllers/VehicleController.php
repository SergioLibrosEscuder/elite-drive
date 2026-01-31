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

    public function store(Request $request)
    {
        $data = $request->validate([
            'hourly_price' => 'required|numeric',
            'manufacturing_year' => 'required|integer',
            'traction' => 'required|string',
            'transmission' => 'required|string',
            'engine' => 'required|string',
            'engine_capacity' => 'required|integer',
            'description' => 'required|string',
            'doors' => 'required|integer',
            'license_plate' => 'required|string|unique:vehicles',
            'brand' => 'required|string',
            'model' => 'required|string',
            'fuel_type' => 'required|string',
            'color' => 'required|string',
            'status' => 'required|string',
        ]);

        $vehicle = Vehicle::create($data);
        return response()->json($vehicle, 201);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $data = $request->validate([
            'hourly_price' => 'required|numeric',
            'manufacturing_year' => 'required|integer',
            'traction' => 'required|string',
            'transmission' => 'required|string',
            'engine' => 'required|string',
            'engine_capacity' => 'required|integer',
            'description' => 'required|string',
            'doors' => 'required|integer',
            'license_plate' => 'required|string|unique:vehicles,license_plate,' . $id,
            'brand' => 'required|string',
            'model' => 'required|string',
            'fuel_type' => 'required|string',
            'color' => 'required|string',
            'status' => 'required|string',
        ]);

        $vehicle->update($data);
        return response()->json($vehicle);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $vehicle->delete();
        return response()->json(['message' => 'Vehicle deleted']);
    }
}

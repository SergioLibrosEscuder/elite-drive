<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
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

        $thmPath = public_path("images/cars/thumbnails/{$id}-thm.webp");
        $cvrPath = public_path("images/cars/covers/{$id}-cvr.webp");

        if (File::exists($thmPath)) {
            File::delete($thmPath);
        }

        if (File::exists($cvrPath)) {
            File::delete($cvrPath);
        }

        return response()->json(['message' => 'Vehicle and associated images deleted']);
    }

    public function uploadImages(Request $request, $id)
    {
        $request->validate([
            'thumbnail' => 'nullable|image|mimes:webp,jpeg,png,jpg|max:10240',
            'cover' => 'nullable|image|mimes:webp,jpeg,png,jpg|max:10240',
        ]);

        $thmPath = public_path('images/cars/thumbnails/');
        $cvrPath = public_path('images/cars/covers/');

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = $id . "-thm.webp";
            $thumbnail->move($thmPath, $thumbnailName);
        }

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $coverName = $id . "-cvr.webp";
            $cover->move($cvrPath, $coverName);
        }

        return response()->json(['message' => 'Images uploaded successfully']);
    }
}

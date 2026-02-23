<?php
// <!-- Sergio Libros -->

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    // Get all vehicles
    public function index()
    {
        // Get all vehicles from database
        $vehicles = Vehicle::all();
        return response()->json($vehicles);
    }

    // Get a specific vehicle
    public function show($id)
    {
        // Get a specific vehicle from database
        $vehicle = Vehicle::findOrFail($id);
        // If the vehicle exists, return it
        if ($vehicle) {
            return response()->json($vehicle);
        }
    }

    // Create a new vehicle
    public function store(Request $request)
    {
        // Validate recieved data
        $data = $request->validate([
            'hourly_price' => 'required|numeric',
            'manufacturing_year' => 'required|integer',
            'traction' => 'required|string',
            'transmission' => 'required|string',
            'engine' => 'required|string',
            'engine_capacity' => 'required|integer',
            'description' => 'required|string',
            'doors' => 'required|integer',
            // Ensure license plate is not duplicated
            'license_plate' => 'required|string|unique:vehicles',
            'brand' => 'required|string',
            'model' => 'required|string',
            'fuel_type' => 'required|string',
            'color' => 'required|string',
            'status' => 'required|string',
        ]);
        // Create a new vehicle with the validated data
        $vehicle = Vehicle::create($data);
        return response()->json($vehicle, 201);
    }

    // Update vehicle data
    public function update(Request $request, $id)
    {
        // Get the vehicle to update
        $vehicle = Vehicle::findOrFail($id);

        // Validate recieved data
        $data = $request->validate([
            'hourly_price' => 'required|numeric',
            'manufacturing_year' => 'required|integer',
            'traction' => 'required|string',
            'transmission' => 'required|string',
            'engine' => 'required|string',
            'engine_capacity' => 'required|integer',
            'description' => 'required|string',
            'doors' => 'required|integer',
            // Set the license plate automatically
            'license_plate' => 'required|string|unique:vehicles,license_plate,' . $id,
            'brand' => 'required|string',
            'model' => 'required|string',
            'fuel_type' => 'required|string',
            'color' => 'required|string',
            'status' => 'required|string',
        ]);
        // Update the vehicle with the validated data
        $vehicle->update($data);
        return response()->json($vehicle);
    }

    // Delete a vehicle
    public function destroy($id)
    {
        // Get the vehicle to delete
        $vehicle = Vehicle::findOrFail($id);
        // Delete the vehicle from database
        $vehicle->delete();

        // Get associated images
        $thmPath = public_path("images/cars/thumbnails/{$id}-thm.webp");
        $cvrPath = public_path("images/cars/covers/{$id}-cvr.webp");

        // Delete associated images if exist
        if (File::exists($thmPath)) {
            File::delete($thmPath);
        }

        if (File::exists($cvrPath)) {
            File::delete($cvrPath);
        }

        return response()->json(['message' => 'Vehicle and associated images deleted']);
    }

    // Upload vehicle images
    public function uploadImages(Request $request, $id)
    {
        // Validate recieved images
        $request->validate([
            'thumbnail' => 'nullable|image|mimes:webp,jpeg,png,jpg|max:10240',
            'cover' => 'nullable|image|mimes:webp,jpeg,png,jpg|max:10240',
        ]);

        // Set images path
        $thmPath = public_path('images/cars/thumbnails/');
        $cvrPath = public_path('images/cars/covers/');

        // Upload thumbnail image if recieved
        if ($request->hasFile('thumbnail')) {
            // Get the thumbnail image
            $thumbnail = $request->file('thumbnail');
            // Set storage name
            $thumbnailName = $id . "-thm.webp";
            // Move the thumbnail to the storage path
            $thumbnail->move($thmPath, $thumbnailName);
        }

        // Upload cover image if recieved
        if ($request->hasFile('cover')) {
            // Get the cover image
            $cover = $request->file('cover');
            // Set storage name
            $coverName = $id . "-cvr.webp";
            // Move the cover to the storage path
            $cover->move($cvrPath, $coverName);
        }

        return response()->json(['message' => 'Images uploaded successfully']);
    }
}

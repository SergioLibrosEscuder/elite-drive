<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Return only customers
    public function index()
    {
        return User::where('role', 'customer')->get();
    }

    // Create a new customer
    public function store(Request $request)
    {
        $data = $request->validate([
            'dni' => 'required|unique:users',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'second_last_name' => 'nullable|string|max:100',
            'birth_date' => 'required|date',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ]);

        // Hash password to encrypt it
        $data['password'] = Hash::make($data['password']);
        // Force role to be customer
        $data['role'] = 'customer';

        $user = User::create($data);
        return response()->json($user, 201);
    }

    // Update customer data
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate data recieved
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable',
            'address' => 'nullable'
        ]);

        $user->update($data);
        return response()->json($user);
    }

    // Delete customer
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}

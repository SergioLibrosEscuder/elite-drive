<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Listar solo clientes
    public function index()
    {
        return User::where('role', 'customer')->get();
    }

    // Crear nuevo cliente
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

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'customer'; // Forzado por defecto

        $user = User::create($data);
        return response()->json($user, 201);
    }

    // Actualizar cliente
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'nullable',
            'address' => 'nullable'
        ]);

        $user->update($data);
        return response()->json($user);
    }

    // Eliminar cliente
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
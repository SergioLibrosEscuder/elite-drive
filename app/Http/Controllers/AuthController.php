<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Seguridad para web.php
            $user = Auth::user();
            
            return response()->json([
                'status' => 'success',
                'user' => [
                    'first_name' => $user->first_name,
                    'role' => $user->role
                ]
            ]);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'dni' => 'required|string|unique:users',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'second_last_name' => 'nullable|string|max:100',
            'birth_date' => 'required|date|before:-18 years', // Validación extra en Laravel
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed', // 'confirmed' busca password_confirmation
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        $user = User::create([
            'dni' => $data['dni'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'birth_date' => $data['birth_date'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'role' => 'customer', // Siempre customer
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user(); // Obtenemos al usuario autenticado por la sesión
        
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string',
            'address'    => 'nullable|string',
        ]);

        $user->update($data);

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password', // Valida que la actual sea correcta
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }
}


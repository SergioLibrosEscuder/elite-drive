<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
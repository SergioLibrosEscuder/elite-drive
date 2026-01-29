<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// API/Actions Routes
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    // Ruta para obtener los datos del usuario logueado al cargar la página
    Route::get('/user/me', function () {
        return Auth::user();
    });
});

// Wildcard Route
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
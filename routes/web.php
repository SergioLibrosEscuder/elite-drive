<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// API/Actions Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    // Ruta para obtener los datos del usuario logueado al cargar la página
    Route::get('/user/me', function () {
        return Auth::user();
    });
    Route::get('/admin/customers', [AdminController::class, 'index']);
    Route::post('/admin/customers', [AdminController::class, 'store']);
    Route::put('/admin/customers/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/customers/{id}', [AdminController::class, 'destroy']);
});

// Wildcard Route
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
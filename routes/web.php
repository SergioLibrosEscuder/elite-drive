<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ReservationController;

// API/Actions Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    Route::get('/user/reservations', [ReservationController::class, 'userReservations']);
    // Ruta para obtener los datos del usuario logueado al cargar la página
    Route::get('/user/me', function () {
        return Auth::user();
    });

    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

    Route::get('/admin/customers', [AdminController::class, 'index']);
    Route::post('/admin/customers', [AdminController::class, 'store']);
    Route::put('/admin/customers/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/customers/{id}', [AdminController::class, 'destroy']);

    Route::post('/cars', [VehicleController::class, 'store']);
    Route::put('/cars/{id}', [VehicleController::class, 'update']);
    Route::delete('/cars/{id}', [VehicleController::class, 'destroy']);

    Route::post('/cars/{id}/images', [VehicleController::class, 'uploadImages']);
});

// Wildcard Route
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

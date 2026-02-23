<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ReservationController;

// API/Actions Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// Restore password route
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::get('/reset-password/{token}', function () {
    return view('app');
})->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
// Verification route
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Only accesible if authenticated user
Route::middleware('auth')->group(function () {
    // User profile routes
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    Route::get('/user/reservations', [ReservationController::class, 'userReservations']);
    // Get current user data route
    Route::get('/user/me', function () {
        return Auth::user();
    });
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
    // Admin routes for managing reservations
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);
    // Routes for confirming and canceling reservations
    Route::put('/reservations/{id}/confirm', [ReservationController::class, 'confirm']);
    Route::put('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);
    // Admin routes for managing customers
    Route::get('/admin/customers', [AdminController::class, 'index']);
    Route::post('/admin/customers', [AdminController::class, 'store']);
    Route::put('/admin/customers/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/customers/{id}', [AdminController::class, 'destroy']);
    // Admin routes for managing cars
    Route::post('/cars', [VehicleController::class, 'store']);
    Route::put('/cars/{id}', [VehicleController::class, 'update']);
    Route::delete('/cars/{id}', [VehicleController::class, 'destroy']);

    Route::post('/cars/{id}/images', [VehicleController::class, 'uploadImages']);
});

// Wildcard Route
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

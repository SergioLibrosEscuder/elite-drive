<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;

Route::post('/login', [AuthController::class, 'login']);

// Public routes to get cars
Route::get("/cars", [VehicleController::class, 'index']);
Route::get("/cars/{id}", [VehicleController::class, 'show']);

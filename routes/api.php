<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Test API
Route::get('/test', function (Request $request) {
    return response()->json(['message' => 'API works!']);
});

// Current user (protected)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::post('/register/customer', [AuthController::class, 'registerCustomer']);
Route::post('/register/agency', [AuthController::class, 'registerAgency']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/refresh-token', [AuthController::class, 'refresh'])->middleware('auth:sanctum');

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\AgenciesController as CustomerAgenciesController;
use App\Http\Controllers\Customer\CarsController as CustomerCarsController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;

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

Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [CustomerProfileController::class, 'profile']);
    Route::post('/profile', [CustomerProfileController::class, 'update']);
    Route::get('/home', [CustomerHomeController::class, 'index']);
    Route::apiResource('/agencies', CustomerAgenciesController::class);
    Route::get('/agencies/{id}/cars', [CustomerCarsController::class, 'agenciesCars']);
    Route::get('/cars/{id}', [CustomerCarsController::class, 'show']);
});

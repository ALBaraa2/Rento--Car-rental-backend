<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\AgenciesController as CustomerAgenciesController;
use App\Http\Controllers\Customer\CarsController as CustomerCarsController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;

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

Route::prefix('customer')->middleware('auth:sanctum')->name('customer.')->group(function () {
    Route::get('/profile', [CustomerProfileController::class, 'profile'])->name('profile');
    Route::get('/profile/update', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => new CustomerResource($request->user())
        ]);
    })->name('profile.update');
    Route::put('/profile', [CustomerProfileController::class, 'update']);
    Route::get('/home', [CustomerHomeController::class, 'index'])->name('home');
    Route::get('/home/search', [CustomerHomeController::class, 'search'])->name('home.search');
    Route::get('agencies/search', [CustomerAgenciesController::class, 'search'])->name('agencies.search');
    Route::apiResource('/agencies', CustomerAgenciesController::class)->except('store', 'update', 'destroy');
    Route::get('/agencies/{id}/cars', [CustomerCarsController::class, 'agencyCars'])->name('agency.cars');
    Route::get('/agencies/{id}/cars/search', [CustomerCarsController::class, 'search'])->name('agencies.cars.search');
    Route::get('/cars/{id}', [CustomerCarsController::class, 'show'])->name('car.details');
});

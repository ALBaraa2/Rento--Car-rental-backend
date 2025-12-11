<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\AgenciesController as CustomerAgenciesController;
use App\Http\Controllers\Customer\CarsController as CustomerCarsController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Resources\CustomerResource;
use App\Http\Controllers\Agency\ProfileController as AgencyProfileController;
use App\Http\Resources\AgencyResource;
use App\Http\Controllers\Agency\HomeController as AgencyHomeController;
use App\Http\Controllers\Agency\CarController as AgencyCarController;
use Illuminate\Support\Facades\Auth;

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
            'user' => new CustomerResource($request->user()->customer),
        ]);
    })->name('show.profile.update');
    Route::put('/profile', [CustomerProfileController::class, 'update']);
    Route::get('/home', [CustomerHomeController::class, 'index'])->name('home');
    Route::get('/home/search', [CustomerHomeController::class, 'search'])->name('home.search');
    Route::get('agencies/search', [CustomerAgenciesController::class, 'search'])->name('agencies.search');
    Route::apiResource('/agencies', CustomerAgenciesController::class)->except('store', 'update', 'destroy');
    Route::get('/agencies/{id}/cars', [CustomerCarsController::class, 'agencyCars'])->name('agency.cars');
    Route::get('/agencies/{id}/cars/search', [CustomerCarsController::class, 'search'])->name('agencies.cars.search');
    Route::get('/cars/{id}', [CustomerCarsController::class, 'show'])->name('car.details');
    Route::get('/cars/{id}/book', [CustomerCarsController::class, 'book'])->name('car.book');
});

Route::prefix('agency')->middleware('auth:sanctum')->name('agency.')->group(function () {
    Route::get('/profile', [AgencyProfileController::class, 'profile'])->name('profile');
    Route::get('/profile/update', function (Request $request) {
        if (Auth::user()->role == 'agency') {
            return response()->json([
                'success' => true,
                'agency' => new AgencyResource($request->user()->agency)
            ]);
        } else
            return response()->json(['message' => 'unauthorized']);
    })->name('show.profile.update');
    Route::put('/profile', [AgencyProfileController::class, 'update']);
    Route::get('/home', [AgencyHomeController::class, 'index'])->name('home');
    Route::get('/cars', [AgencyCarController::class, 'index'])->name('cars');
    Route::get('/cars/create', [AgencyCarController::class, 'getTypes'])->name('cars.getTypes');
    Route::get('/cars/get/{type}', [AgencyCarController::class, 'getBrandName'])->name('cars.get.brandsName');
    Route::get('/cars/get/{type}/{brandId}', [AgencyCarController::class, 'getModels'])->name('cars.get.models');
    Route::get('/cars/getTransmission', [AgencyCarController::class, 'getTransmission'])->name('cars.getTransmission');
    Route::get('/cars/getColor', [AgencyCarController::class, 'getColor'])->name('cars.getColor');
    Route::get('/cars/getFuelType', [AgencyCarController::class, 'getFuelType'])->name('cars.getFuelType');
    Route::post('/cars/store', [AgencyCarController::class,'store'])->name('cars.store');
});

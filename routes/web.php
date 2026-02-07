<?php

use App\Http\Controllers\admin\AgenciesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/agencies', [AgenciesController::class, 'index'])->name('AllAgencies');

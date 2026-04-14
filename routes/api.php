<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.bearer')->group(function () {
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::apiResource('/companies', CompanyController::class);
    Route::apiResource('/projects', ProjectController::class);
});

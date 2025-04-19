<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AgentAvailabilityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('companies')->group(function () {
    Route::post('/', [CompanyController::class, 'store']);
    Route::put('/{company}', [CompanyController::class, 'updateRegisteredAgent']);
});

Route::get('/agent-availability/{state}', [AgentAvailabilityController::class, 'check']);

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
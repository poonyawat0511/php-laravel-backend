<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CooperativeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/cooperatives', [CooperativeController::class, 'store']);
    Route::get('/cooperatives/me', [CooperativeController::class, 'myRequests']);

});

Route::middleware(['auth:sanctum', 'staff'])->group(function () {
    Route::get('/staff/cooperatives', [CooperativeController::class, 'index']);
    Route::patch('/staff/cooperatives/{id}/review', [CooperativeController::class, 'review']);

});
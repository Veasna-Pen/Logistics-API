<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShipmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    Route::prefix('shipments')->group(function () {
        Route::post('/', [ShipmentController::class, 'store']);
        Route::get('/', [ShipmentController::class, 'index']);
        Route::get('/{id}', [ShipmentController::class, 'show']);

        Route::post('/{id}/status', [ShipmentController::class, 'updateStatus']);

        Route::post('/{id}/assign-driver', [ShipmentController::class, 'assignDriver']);
    });
});

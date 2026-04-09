<?php

use App\Http\Controllers\Api\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('shipments')->group(function () {
    Route::post('/', [ShipmentController::class, 'store']);
    Route::get('/', [ShipmentController::class, 'index']);
    Route::get('/{id}', [ShipmentController::class, 'show']);

    Route::post('/{id}/status', [ShipmentController::class, 'updateStatus']);

    Route::post('/{id}/assign-driver', [ShipmentController::class, 'assignDriver']);
    Route::get('/{id}/logs', [ShipmentController::class, 'show']); //logs
});

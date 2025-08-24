<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('transactions')->group(function () {
    Route::post('/upload', [TransactionController::class, 'upload']);
    Route::get('/summary', [TransactionController::class, 'summary']);
    Route::get('/', [TransactionController::class, 'index']);
});

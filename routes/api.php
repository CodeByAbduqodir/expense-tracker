<?php
use App\Http\Controllers\Api\TransactionController;
dd("hello");
Route::get('/transactions', [TransactionController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);
Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
Route::get('/balance', [TransactionController::class, 'balance']);
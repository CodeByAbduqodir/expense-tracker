<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/', [TransactionController::class, 'index'])->name('tracker.index');
Route::post('/transaction', [TransactionController::class, 'store'])->name('tracker.store');
Route::delete('/transaction/{id}', [TransactionController::class, 'destroy'])->name('tracker.destroy');
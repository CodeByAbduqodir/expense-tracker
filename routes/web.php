<?php
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TransactionController::class, 'index'])->name('tracker.index');
Route::post('/transaction', [TransactionController::class, 'store'])->name('tracker.store');
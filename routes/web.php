<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\GudangInController;
use App\Http\Controllers\GudangOutController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/stock', [StockController::class, 'index']);
Route::post('/stock', [StockController::class, 'store']);

Route::get('/gudang-in', [GudangInController::class, 'index']);
Route::post('/gudang-in', [GudangInController::class, 'store']);

Route::get('/gudang-out', [GudangOutController::class, 'index']);
Route::post('/gudang-out', [GudangOutController::class, 'store']);
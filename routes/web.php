<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\GudangInController;
use App\Http\Controllers\GudangOutController;
use App\Http\Controllers\TransaksiKasController;
use App\Http\Controllers\LaporanMingguanController;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/stock', [StockController::class, 'index']);
Route::post('/stock', [StockController::class, 'store']);

Route::get('/gudang-in', [GudangInController::class, 'index']);
Route::post('/gudang-in', [GudangInController::class, 'store']);

Route::get('/gudang-out', [GudangOutController::class, 'index']);
Route::post('/gudang-out', [GudangOutController::class, 'store']);

// Route untuk mendapatkan kode barang berdasarkan jenis peralatan
Route::get('/get-stock-codes/{type}', [StockController::class, 'getStockCodes']);

Route::get('/transaksi-kas', [TransaksiKasController::class, 'index']);
Route::post('/transaksi-kas', [TransaksiKasController::class, 'store']);

Route::get('/laporan-mingguan', [LaporanMingguanController::class, 'index'])->name('laporan_mingguan.index');
Route::get('/laporan-mingguan/create', [LaporanMingguanController::class, 'create'])->name('laporan_mingguan.create');
Route::post('/laporan-mingguan', [LaporanMingguanController::class, 'store'])->name('laporan_mingguan.store');

Route::resource('transaksi', TransaksiController::class);
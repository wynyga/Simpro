<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\{
    StockController,
    GudangInController,
    GudangOutController,
    TransaksiKasController,
    LaporanMingguanController,
    TransaksiController,
    BlokUnitController,
    UserController,
    PerumahanController,
    TipeRumahController
};

// Authentication Routes
Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
});

// Stock Management
Route::prefix('/stock')->group(function () {
    Route::get('/', [StockController::class, 'index']);
    Route::post('/', [StockController::class, 'store']);
    Route::get('/codes/{type}', [StockController::class, 'getStockCodes']);
});

// Gudang Routes
Route::prefix('/gudang')->group(function () {
    Route::post('/in', [GudangInController::class, 'store']);
    Route::post('/out', [GudangOutController::class, 'store']);
});

// Transaction Routes
Route::prefix('/transaksi')->group(function () {
    Route::get('/kas', [TransaksiKasController::class, 'index']);
    Route::post('/kas', [TransaksiKasController::class, 'store']);
    Route::put('/{id}', [TransaksiController::class, 'update']); //Coming Soon
    Route::delete('/{id}', [TransaksiController::class, 'destroy']); //Coming Soon
});

// Weekly Report Routes
Route::prefix('/laporan-mingguan')->group(function () {
    Route::get('/', [LaporanMingguanController::class, 'index'])->name('laporan_mingguan.index');
    Route::get('/create', [LaporanMingguanController::class, 'create'])->name('laporan_mingguan.create');
    Route::post('/', [LaporanMingguanController::class, 'store']);
    Route::get('/summary', [LaporanMingguanController::class, 'showSummary'])->name('laporan_mingguan.summary');
    Route::get('/summary/jenis-biaya', [LaporanMingguanController::class, 'showSummaryPerJenisBiaya'])->name('laporan_mingguan.summary_per_jenis_biaya');
    Route::get('/summary/uraian', [LaporanMingguanController::class, 'showSummaryPerUraian'])->name('laporan_mingguan.summary_per_uraian');
    Route::get('/summary/kategori', [LaporanMingguanController::class, 'showSummaryPerKategori'])->name('laporan_mingguan.summary_per_kategori');
});

// Perumahan Routes
Route::prefix('perumahan')->group(function () {
    Route::get('/', [PerumahanController::class, 'index']);
    Route::post('/', [PerumahanController::class, 'store']);
    Route::post('/select', [PerumahanController::class, 'selectPerumahan']);
});

// Tipe Rumah Routes
Route::prefix('/penjualan')->group(function () {
    Route::get('/tipe_rumah', [TipeRumahController::class, 'index']);
    Route::get('/tipe_rumah/create', [TipeRumahController::class, 'create']);
    Route::post('/tipe_rumah', [TipeRumahController::class, 'store']);
    Route::put('/tipe_rumah/{id}', [TipeRumahController::class, 'update']);
    Route::delete('/tipe_rumah/{id}', [TipeRumahController::class, 'destroy']);

    Route::get('/transaksi',[TransaksiController::class,'index']);
    Route::post('/transaksi',[TransaksiController::class,'store']);
    Route::patch('/transaksi/{id}',[TransaksiController::class,'update']);
    Route::delete('/transaksi/{id}',[TransaksiController::class,'destroy']);
    Route::get('/transaksi/create',[TransaksiController::class,'create']);
});

// User and Blok Unit Management
Route::prefix('/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
});
Route::prefix('/blokunit')->group(function () {
    Route::get('/', [BlokUnitController::class, 'index'])->name('blokunit.index');
    Route::get('/create', [BlokUnitController::class, 'create'])->name('blokunit.create');
    Route::post('/store', [BlokUnitController::class, 'store'])->name('blokunit.store');
});

// Testing route
Route::get('/testing', function () {
    return response()->json([
        "message" => "Get method berhasil"
    ]);
});

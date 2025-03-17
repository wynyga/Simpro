<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\{
    StockController,
    GudangInController,
    GudangOutController,
    TransaksiKasController,
    TransaksiController,
    UserController,
    PerumahanController,
    TipeRumahController,
    UnitController,
    BlokController,
    GudangController,
    LapBulananController,
    CostStructureController,
    CostCentreController,
    CostElementController,
    CostTeeController
};

// Authentication Routes
Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
});

// Stock Management
Route::prefix('/stock')->group(function () {
    Route::get('/', [StockController::class, 'index']);
    Route::post('/', [StockController::class, 'store']);
    Route::get('/codes/{type}', [StockController::class, 'getStockCodes']);
    Route::patch('/{kode_barang}', [StockController::class, 'update']);
    Route::delete('/{id}', [StockController::class, 'destroy']);
    Route::get('/search',[StockController::class, 'searchStock']);
});

// Gudang Routes
Route::prefix('/gudang')->group(function () {
    Route::post('/in', [GudangInController::class, 'store']);
    Route::post('/in/verify/{id}', [GudangInController::class, 'verify'])->middleware('role:Manager'); 
    Route::post('/in/reject/{id}', [GudangInController::class, 'reject'])->middleware('role:Manager'); 
    Route::post('/out', [GudangOutController::class, 'store']);
    Route::post('/out/verify/{id}', [GudangOutController::class, 'verify'])->middleware('role:Manager'); 
    Route::post('/out/reject/{id}', [GudangOutController::class, 'reject'])->middleware('role:Manager'); 
    Route::get('/all', [GudangController::class, 'index']); 
});

// Transaction Routes
Route::prefix('/transaksi')->group(function () {
    Route::get('/kas', [TransaksiKasController::class, 'index']);
    Route::post('/kas', [TransaksiKasController::class, 'store']);
    Route::post('/kas/{id}/verify', [TransaksiKasController::class, 'approveTransaction'])->middleware('role:Manager');
    Route::post('/kas/{id}/reject', [TransaksiKasController::class, 'rejectTransaction'])->middleware('role:Manager');
    Route::get('/kas/history', [TransaksiKasController::class, 'getHistory'])->middleware('role:Manager');
});

// Perumahan Routes
Route::prefix('perumahan')->group(function () {
    Route::get('/', [PerumahanController::class, 'index']);
    Route::post('/', [PerumahanController::class, 'store']);
    Route::post('/select', [PerumahanController::class, 'selectPerumahan']);
});

// Tipe Rumah Routes
Route::prefix('penjualan')->group(function () {
    Route::prefix('/tipe_rumah')->group(function () {
        Route::get('/', [TipeRumahController::class, 'index']);
        Route::get('/{id}', [TipeRumahController::class, 'show']);
        Route::get('/create', [TipeRumahController::class, 'create']);
        Route::post('/add', [TipeRumahController::class, 'store']);
        Route::put('/{id}', [TipeRumahController::class, 'update']);
        Route::delete('/{id}', [TipeRumahController::class, 'destroy']);
    });

    // Blok Routes
    Route::prefix('/blok')->group(function () {
        Route::get('/', [BlokController::class, 'index'])->name('blok.index');
        Route::post('/store', [BlokController::class, 'store'])->name('blok.store');
        Route::get('/{id}', [BlokController::class, 'show'])->name('blok.show');
        Route::put('/{id}', [BlokController::class, 'update'])->name('blok.update');
        Route::delete('/{id}', [BlokController::class, 'destroy'])->name('blok.destroy');
    });

    // Unit Routes
    Route::prefix('/unit')->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('unit.index');
        Route::post('/store', [UnitController::class, 'store'])->name('unit.store');
        Route::get('/{id}', [UnitController::class, 'show'])->name('unit.show');
        Route::put('/{id}', [UnitController::class, 'update'])->name('unit.update'); 
        Route::delete('/{id}', [UnitController::class, 'destroy'])->name('unit.destroy');
    });

    // Nested prefix for Transaksi management
    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index']);
        Route::get('/create', [TransaksiController::class, 'create']);
        Route::post('/store', [TransaksiController::class, 'store']);
        Route::patch('/{id}', [TransaksiController::class, 'update']);
        Route::delete('/{id}', [TransaksiController::class, 'destroy']);
    });
});

// User and Blok Unit Management
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->middleware('cors');
    Route::post('/add', [UserController::class, 'store']);
    Route::put('/{id}',[UserController::class, 'update'])->middleware('cors');
    Route::delete('/{id}',[UserController::class, 'destroy'])->middleware('cors');
});

Route::prefix('cost_center')->group(function () {
    Route::get('/', [CostCentreController::class, 'index']);
    Route::post('/create', [CostCentreController::class, 'store']);
});

Route::prefix('cost_element')->group(function () {
    Route::get('/', [CostElementController::class, 'index']);
    Route::post('/create', [CostElementController::class, 'store']);
});

Route::prefix('cost_tee')->group(function () {
    Route::get('/', [CostTeeController::class, 'index']);
    Route::post('/create', [CostTeeController::class, 'store']);
});

Route::prefix('cost_structure')->group(function () {
    Route::get('/', [CostStructureController::class, 'index']);
    Route::post('/create', [CostStructureController::class, 'store']);
});

Route::prefix('lap_bulanan')->group(function () {
    Route::get('/', [LapBulananController::class, 'index']);
    Route::post('/create', [LapBulananController::class, 'store']);
    Route::get('/kas_masuk/{bulan}/{tahun}', [LapBulananController::class, 'getKasMasuk']);
    Route::get('/kas_keluar/{bulan}/{tahun}', [LapBulananController::class, 'getKasKeluar']);
    Route::get('/sisa_kas/{bulan}/{tahun}', [LapBulananController::class, 'getSisaKasProject']);
    Route::get('/journal/{bulan}/{tahun}', [TransaksiKasController::class, 'getJournalSummary']);
    Route::get('/inventory', [StockController::class, 'getStockInventory']);
    Route::get('/summary/{bulan}/{tahun}', [GudangOutController::class, 'getGudangOutSummary']);

});

// Testing route
// Route::get('/testing', function () {
//     return response()->json([
//         "message" => "Get method berhasil"
//     ]);
// });

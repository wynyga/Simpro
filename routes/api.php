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
    CostTeeController,
    PenjualanStatusController,
    KwitansiController,
    STTBController,
};

// Authentication Routes
Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('auth.updateProfile');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('auth.changePassword');
    Route::get('/users', [AuthController::class, 'getUsers'])->name('auth.getUsers');
    Route::post('/reset-password/{id}', [AuthController::class, 'resetUserPassword'])->name('auth.resetUserPassword');
    Route::delete('/users/{id}', [AuthController::class, 'deleteUser'])->name('auth.deleteUser')->middleware('auth:api');
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
    Route::get('/in/{id}', [GudangInController::class, 'show']);
});

// Transaction Routes
Route::prefix('/transaksi')->group(function () {
    Route::get('/kas', [TransaksiKasController::class, 'index']);
    Route::post('/kas', [TransaksiKasController::class, 'store']);
    Route::get('/kas/ringkasan/{tahun?}', [TransaksiKasController::class, 'getRingkasanKasPerTahun']);
    Route::post('/kas/{id}/verify', [TransaksiKasController::class, 'approveTransaction'])->middleware('role:Manager');
    Route::post('/kas/{id}/reject', [TransaksiKasController::class, 'rejectTransaction'])->middleware('role:Manager');
    Route::get('/kas/history', [TransaksiKasController::class, 'getHistory'])->middleware('role:Manager');
});

// Perumahan Routes
Route::prefix('perumahan')->group(function () {
    Route::get('/', [PerumahanController::class, 'index']);
    Route::get('/{id}', [PerumahanController::class, 'show']);
    Route::post('/store', [PerumahanController::class, 'store']);
    Route::post('/select', [PerumahanController::class, 'selectPerumahan']);
    Route::put('/update/{id}', [PerumahanController::class, 'update']);
    Route::delete('/delete/{id}', [PerumahanController::class, 'destroy']);
    Route::post('/change', [PerumahanController::class, 'changePerumahan']);
});

// Tipe Rumah Routes
Route::prefix('penjualan')->group(function () {
    Route::prefix('/tipe_rumah')->group(function () {
        Route::get('/', [TipeRumahController::class, 'index']);
        Route::get('/all', [TipeRumahController::class, 'all']);
        Route::get('/{id}', [TipeRumahController::class, 'show']);
        Route::get('/create', [TipeRumahController::class, 'create']);
        Route::post('/add', [TipeRumahController::class, 'store']);
        Route::put('/{id}', [TipeRumahController::class, 'update']);
        Route::delete('/{id}', [TipeRumahController::class, 'destroy']);
    });

    // Blok Routes
    Route::prefix('/blok')->group(function () {
        Route::get('/', [BlokController::class, 'index'])->name('blok.index');
        Route::get('/all', [BlokController::class, 'all']);
        Route::post('/store', [BlokController::class, 'store'])->name('blok.store');
        Route::get('/{id}', [BlokController::class, 'show'])->name('blok.show');
        Route::put('/{id}', [BlokController::class, 'update'])->name('blok.update');
        Route::delete('/{id}', [BlokController::class, 'destroy'])->name('blok.destroy');
    });

    // Unit Routes
    Route::prefix('/unit')->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('unit.index');
        Route::get('/all', [UnitController::class, 'all']);
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
    Route::get('/all', [UserController::class, 'all']);
    Route::post('/add', [UserController::class, 'store']);
    Route::put('/{id}',[UserController::class, 'update'])->middleware('cors');
    Route::delete('/{id}',[UserController::class, 'destroy'])->middleware('cors');
});

// Routes untuk Cost Centre
Route::prefix('cost_center')->middleware('auth')->group(function () {
    Route::get('/', [CostCentreController::class, 'index'])->name('cost_center.index');
    Route::post('/create', [CostCentreController::class, 'store'])->name('cost_center.store');
    Route::put('/update/{id}', [CostCentreController::class, 'update'])->name('cost_center.update');
    Route::delete('/delete/{id}', [CostCentreController::class, 'destroy'])->name('cost_center.delete');
});

// Routes untuk Cost Element
Route::prefix('cost_element')->middleware('auth')->group(function () {
    Route::get('/', [CostElementController::class, 'index'])->name('cost_element.index');
    Route::post('/create', [CostElementController::class, 'store'])->name('cost_element.store');
    Route::put('/update/{id}', [CostElementController::class, 'update'])->name('cost_element.update');
    Route::delete('/delete/{id}', [CostElementController::class, 'destroy'])->name('cost_element.delete');
});

// Routes untuk Cost Tee
Route::prefix('cost_tee')->middleware('auth')->group(function () {
    Route::get('/', [CostTeeController::class, 'index'])->name('cost_tee.index');
    Route::get('/{id}', [CostStructureController::class, 'show'])->name('cost_structure.index');
    Route::post('/create', [CostTeeController::class, 'store'])->name('cost_tee.store');
    Route::put('/update/{id}', [CostTeeController::class, 'update'])->name('cost_tee.update');
    Route::delete('/delete/{id}', [CostTeeController::class, 'destroy'])->name('cost_tee.delete');
});

// Routes untuk Cost Structure
Route::prefix('cost_structure')->middleware('auth')->group(function () {
    Route::get('/', [CostStructureController::class, 'index'])->name('cost_structure.index');
    Route::get('/{id}', [CostStructureController::class, 'show'])->name('cost_structure.index');
    Route::post('/create', [CostStructureController::class, 'store'])->name('cost_structure.store');
    Route::put('/update/{id}', [CostStructureController::class, 'update'])->name('cost_structure.update');
    Route::delete('/delete/{id}', [CostStructureController::class, 'destroy'])->name('cost_structure.delete');
});

// Routes untuk Laporan Bulanan
Route::prefix('lap_bulanan')->middleware('auth')->group(function () {
    Route::get('/', [LapBulananController::class, 'index'])->name('lap_bulanan.index');
    Route::post('/create', [LapBulananController::class, 'store'])->name('lap_bulanan.store');
    Route::put('/update/{id}', [LapBulananController::class, 'update'])->name('lap_bulanan.update');
    Route::delete('/delete/{id}', [LapBulananController::class, 'destroy'])->name('lap_bulanan.delete');
    Route::get('/kas_masuk/{bulan}/{tahun}', [LapBulananController::class, 'getKasMasuk'])->name('lap_bulanan.kas_masuk');
    Route::get('/kas_keluar/{bulan}/{tahun}', [LapBulananController::class, 'getKasKeluar'])->name('lap_bulanan.kas_keluar');
    Route::get('/sisa_kas/{bulan}/{tahun}', [LapBulananController::class, 'getSisaKasProject'])->name('lap_bulanan.sisa_kas');
    Route::get('/history', [LapBulananController::class, 'getHistory'])->name('lap_bulanan.history');
    Route::prefix('transaksi_kas')->group(function () {
        Route::get('/journal/{bulan}/{tahun}', [TransaksiKasController::class, 'getJournalSummary'])->name('transaksi_kas.journal_summary');
    });
    Route::prefix('inventory')->group(function () {
        Route::get('/{bulan}/{tahun}', [StockController::class, 'getStockInventory'])->name('inventory.stock');
    });
    Route::prefix('gudang')->middleware('auth')->group(function () {
        Route::get('/summary/{bulan}/{tahun}', [GudangOutController::class, 'getGudangOutSummary'])->name('gudang.summary');
    });
    Route::get('/tahunan/{tahun}', [LapBulananController::class, 'getLaporanTahunan']);

});

Route::prefix('kwitansi')->middleware('auth')->group(function () {
    Route::post('store', [KwitansiController::class, 'store']); // simpan kwitansi
    Route::get('/{id}', [KwitansiController::class, 'show']); // detail kwitansi
    Route::get('/{id}/cetak', [KwitansiController::class, 'cetak']); // generate PDF
    Route::post('/sttb/store', [STTBController::class, 'store']);
    Route::get('/sttb/{id}', [STTBController::class, 'show']);
    Route::get('/sttb/{id}/cetak', [STTBController::class, 'cetak']);
    Route::get('/cetak-co/{id}', [KwitansiController::class, 'cetakCO']);

});

Route::get('/penjualan/status-bayar', [PenjualanStatusController::class, 'index']);




<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\GudangInController;
use App\Http\Controllers\GudangOutController;
use App\Http\Controllers\TransaksiKasController;
use App\Http\Controllers\LaporanMingguanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BlokUnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PerumahanController;
use App\Http\Controllers\TipeRumahController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/stock', [StockController::class, 'index']);
Route::post('/stock', [StockController::class, 'store']);
// Route untuk mendapatkan kode barang berdasarkan jenis peralatan
Route::get('/get-stock-codes/{type}', [StockController::class, 'getStockCodes']);

// Route::get('/gudang-in', [GudangInController::class, 'index']);
Route::post('/gudang-in', [GudangInController::class, 'store']);

// Route::get('/gudang-out', [GudangOutController::class, 'index']);
Route::post('/gudang-out', [GudangOutController::class, 'store']);

// Route untuk transaksi Kas
Route::get('/transaksi-kas', [TransaksiKasController::class, 'index']);
Route::post('/transaksi-kas', [TransaksiKasController::class, 'store']);
Route::get('/api/transaksi-kas', [TransaksiKasController::class, 'getTransaksiKasData']);

//Route untuk laporan mingguan
Route::get('/laporan-mingguan', [LaporanMingguanController::class, 'index'])->name('laporan_mingguan.index');
Route::get('/laporan-mingguan/create', [LaporanMingguanController::class, 'create'])->name('laporan_mingguan.create');
Route::post('/laporan-mingguan', [LaporanMingguanController::class, 'store'])->name('laporan_mingguan.store');
Route::get('/laporan-mingguan/summary', [LaporanMingguanController::class, 'showSummary'])->name('laporan_mingguan.summary');
Route::get('/laporan-mingguan/summary/jenis-biaya', [LaporanMingguanController::class, 'showSummaryPerJenisBiaya'])->name('laporan_mingguan.summary_per_jenis_biaya');
Route::get('/laporan-mingguan/summary/uraian', [LaporanMingguanController::class, 'showSummaryPerUraian'])->name('laporan_mingguan.summary_per_uraian');
Route::get('/laporan-mingguan/summary/kategori', [LaporanMingguanController::class, 'showSummaryPerKategori'])->name('laporan_mingguan.summary_per_kategori');

//Route API Penjualan
Route::get('/penjualan/perumahan', [PerumahanController::class, 'index']);
Route::post('/penjualan/perumahan', [PerumahanController::class, 'store']);
Route::get('/penjualan/tipe_rumah', [TipeRumahController::class, 'index']);
Route::get('/penjualan/tipe_rumah/create', [TipeRumahController::class, 'create']);
Route::post('/penjualan/tipe_rumah', [TipeRumahController::class, 'store']);
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::prefix('blokunit')->group(function () {
    Route::get('/', [BlokUnitController::class, 'index'])->name('blokunit.index');
    Route::get('/create', [BlokUnitController::class, 'create'])->name('blokunit.create');
    Route::post('/store', [BlokUnitController::class, 'store'])->name('blokunit.store');
});
//Route untuk penjualan
 Route::resource('/penjualan/transaksi', TransaksiController::class);
 Route::resource('/penjualan/blokunit', BlokUnitController::class);
// Route::resource('/penjualan/user', UserController::class);
// Route::resource('/penjualan/tipe_rumah', TipeRumahController::class);
// Route::resource('/penjualan/perumahan', PerumahanController::class);


Route::get('/testing', function(){
    return response()->json([
        "message"=>"Get method berhasil"
    ]);
});

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DayWork;
use App\Models\Equipment;
use App\Models\Tools;
use App\Models\LandStoneSand;
use App\Models\Cement;
use App\Models\Rebar;
use App\Models\Wood;
use App\Models\RoofCeilingTile;
use App\Models\KeramikFloor;
use App\Models\PaintGlassWallpaper;
use App\Models\Others;
use App\Models\OilChemicalPerekat;
use App\Models\Sanitary;
use App\Models\PipingPump;
use App\Models\Lighting;
use App\Models\Log;

class GudangOut extends Model
{
    protected $table = 'gudang_out';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'tanggal',
        'peruntukan',
        'jumlah',
        'satuan',
        'jumlah_harga',
        'keterangan',
        'perumahan_id'
    ];

    protected static function booted()
    {
        static::created(function ($gudangOut) {
            $kodeBarang = $gudangOut->kode_barang;
            $jumlahKeluar = $gudangOut->jumlah;

            // Cari tabel yang sesuai berdasarkan kode_barang
            $stock = null;
            if (strpos($kodeBarang, 'MDW10') === 0) {
                $stock = DayWork::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'EQP20') === 0) {
                $stock = Equipment::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'EQT30') === 0) {
                $stock = Tools::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'LSS40') === 0) {
                $stock = LandStoneSand::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'CEM50') === 0) {
                $stock = Cement::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'REB30') === 0) {
                $stock = Rebar::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'WOD70') === 0) {
                $stock = Wood::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'RCT80') === 0) {
                $stock = RoofCeilingTile::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'KERM90') === 0) {
                $stock = KeramikFloor::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'PGW100') === 0) {
                $stock = PaintGlassWallpaper::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'OTH110') === 0) {
                $stock = Others::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'OCP110') === 0) {
                $stock = OilChemicalPerekat::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'SANI120') === 0) {
                $stock = Sanitary::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'PIPP130') === 0) {
                $stock = PipingPump::where('kode', $kodeBarang)->first();
            } elseif (strpos($kodeBarang, 'LIGH140') === 0) {
                $stock = Lighting::where('kode', $kodeBarang)->first();
            } else {
                $stock = null;
            }

            // Jika stok ditemukan, kurangi jumlah
            if ($stock) {
                if ($stock->stock_bahan >= $jumlahKeluar) {
                    $stock->stock_bahan -= $jumlahKeluar;
                    $stock->save();
                } else {
                    // Catat log error jika jumlah keluar lebih besar dari stok
                    Log::create([
                        'kode_barang' => $kodeBarang,
                        'nama_barang' => $gudangOut->nama_barang,
                        'tipe_log' => 'error',
                        'pesan' => 'Jumlah barang keluar lebih besar dari stok yang tersedia.'
                    ]);
                }
            } else {
                // Jika stok tidak ditemukan, catat log error
                Log::create([
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $gudangOut->nama_barang,
                    'tipe_log' => 'error',
                    'pesan' => 'Kode barang tidak ditemukan di salah satu tabel stock.'
                ]);
            }
        });
    }
}

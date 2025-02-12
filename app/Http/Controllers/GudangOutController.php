<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GudangOut;
use App\Helpers\StockHelper; 

class GudangOutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;  // Menggunakan perumahan_id dari profil user
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        $gudangOuts = GudangOut::where('perumahan_id', $perumahanId)->get();
        return response()->json($gudangOuts);
    }
     
    
    public function store(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        // Validasi input (hapus jumlah_harga karena akan dihitung otomatis)
        $validated = $request->validate([
            'kode_barang' => 'required', // Asumsi 'stocks' adalah tabel yang berisi semua jenis barang
            'tanggal' => 'required|date',
            'peruntukan' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable'
        ]);
    
        // Mendapatkan model stok yang sesuai dari StockHelper
        $stockModel = StockHelper::getModelFromCode($request->kode_barang, $perumahanId);
        if (!$stockModel) {
            return response()->json([
                'message' => 'Barang tidak ditemukan di stok.',
            ], 404);
        }
    
        // Periksa apakah jumlah stok cukup
        if ($stockModel->stock_bahan < $request->jumlah) {
            return response()->json([
                'message' => 'Stok tidak cukup.',
            ], 400);
        }
    
        // **Hitung jumlah_harga otomatis** (jumlah barang keluar × harga satuan)
        $jumlahHarga = $request->jumlah * $stockModel->harga_satuan;
    
        // **Gunakan satuan dari tabel stok**
        $satuan = $stockModel->satuan;
    
        // Menyimpan data ke Gudang Out
        $gudangOut = new GudangOut();
        $gudangOut->perumahan_id = $perumahanId;
        $gudangOut->kode_barang = $request->kode_barang;
        $gudangOut->nama_barang = $stockModel->nama_barang; // Mengambil nama barang dari model stok
        $gudangOut->tanggal = $request->tanggal;
        $gudangOut->peruntukan = $request->peruntukan;
        $gudangOut->jumlah = $request->jumlah;
        $gudangOut->satuan = $satuan; // Menggunakan satuan dari stok
        $gudangOut->jumlah_harga = $jumlahHarga; // Menggunakan hasil perhitungan otomatis
        $gudangOut->keterangan = $request->keterangan;
        $gudangOut->save();
    
        // Mengurangi jumlah stok
        $stockModel->stock_bahan -= $request->jumlah;
        $stockModel->save();
    
        return response()->json([
            'message' => 'Data Gudang Out berhasil disimpan dan stok diperbarui.',
            'data' => $gudangOut
        ], 201);
    }
    

    public function getGudangOutSummary($bulan, $tahun)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        // Menghitung total pengeluaran bahan dari gudang dalam bulan & tahun tertentu
        $totalGudangOut = GudangOut::where('perumahan_id', $perumahanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah_harga'); // Mengambil total harga dari bahan yang keluar

        return response()->json([
            'pengeluaran_bahan' => [
                'code_account' => 'GD0104B' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . substr($tahun, -2), // Contoh: GD0104B324
                'total_rp' => number_format($totalGudangOut, 2, ',', '.')
            ]
        ]);
    }

}

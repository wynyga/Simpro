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

        // Validasi input
        $validated = $request->validate([
            'kode_barang' => 'required', // Asumsi 'stocks' adalah tabel yang berisi semua jenis barang
            'tanggal' => 'required|date',
            'peruntukan' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'satuan' => 'required',
            'jumlah_harga' => 'required|numeric',
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

        // Menyimpan data ke Gudang Out
        $gudangOut = new GudangOut($validated);
        $gudangOut->perumahan_id = $perumahanId; 
        $gudangOut->nama_barang = $stockModel->nama_barang; // Mengambil nama barang dari model stok
        $gudangOut->save();

        // Mengurangi jumlah stok
        $stockModel->stock_bahan -= $request->jumlah;
        $stockModel->save();

        return response()->json([
            'message' => 'Data Gudang Out berhasil disimpan dan stok diperbarui.',
            'data' => $gudangOut
        ], 201);
    }
}

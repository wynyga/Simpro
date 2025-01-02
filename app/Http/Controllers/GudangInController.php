<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GudangIn;
use App\Helpers\StockHelper; 

class GudangInController extends Controller
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
    
        $gudangIns = GudangIn::where('perumahan_id', $perumahanId)->get();
        return response()->json($gudangIns);
    }
     
    
    public function store(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;  // Menggunakan perumahan_id dari profil user
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        // Validasi input
        $validated = $request->validate([
            'kode_barang' => 'required',
            'pengirim' => 'required',
            'no_nota' => 'required',
            'tanggal_barang_masuk' => 'required|date',
            'jumlah' => 'required|numeric',
            'satuan' => 'required',
            'harga_satuan' => 'required|numeric',
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

        // Menyimpan data ke Gudang In
        $gudangIn = new GudangIn($validated);
        $gudangIn->perumahan_id = $perumahanId; 
        $gudangIn->nama_barang = $stockModel->nama_barang; // Mengambil nama barang dari model stok
        $gudangIn->save();

        // Update stok
        $stockModel->stock_bahan += $request->jumlah; // Menambah jumlah stok
        $stockModel->save();

        return response()->json([
            'message' => 'Data Gudang In berhasil disimpan dan stok diperbarui.',
            'data' => $gudangIn
        ], 201);
    }
}

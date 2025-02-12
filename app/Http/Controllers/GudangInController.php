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
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        // Validasi input (hapus jumlah_harga karena akan dihitung otomatis)
        $validated = $request->validate([
            'kode_barang' => 'required',
            'pengirim' => 'required',
            'no_nota' => 'required',
            'tanggal_barang_masuk' => 'required|date',
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
    
        // **Hitung jumlah_harga otomatis** (jumlah barang masuk × harga satuan)
        $jumlahHarga = $request->jumlah * $stockModel->harga_satuan;
    
        // **Gunakan satuan dari tabel stok**
        $satuan = $stockModel->satuan;
    
        // Menyimpan data ke Gudang In
        $gudangIn = new GudangIn();
        $gudangIn->perumahan_id = $perumahanId;
        $gudangIn->kode_barang = $request->kode_barang;
        $gudangIn->nama_barang = $stockModel->nama_barang; // Mengambil nama barang dari model stok
        $gudangIn->pengirim = $request->pengirim;
        $gudangIn->no_nota = $request->no_nota;
        $gudangIn->tanggal_barang_masuk = $request->tanggal_barang_masuk;
        $gudangIn->jumlah = $request->jumlah;
        $gudangIn->satuan = $satuan; // Menggunakan satuan dari stok
        $gudangIn->harga_satuan = $stockModel->harga_satuan; // Harga satuan diambil dari stok
        $gudangIn->jumlah_harga = $jumlahHarga; // Menggunakan hasil perhitungan otomatis
        $gudangIn->keterangan = $request->keterangan;
        $gudangIn->save();
    
        // Update stok (menambah jumlah stok)
        $stockModel->stock_bahan += $request->jumlah;
        $stockModel->save();
    
        return response()->json([
            'message' => 'Data Gudang In berhasil disimpan dan stok diperbarui.',
            'data' => $gudangIn
        ], 201);
    }
    
}

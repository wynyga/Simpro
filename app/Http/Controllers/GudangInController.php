<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GudangIn;
use App\Helpers\StockHelper; 

class GudangInController extends Controller
{
    public function store(Request $request)
    {
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
        $stockModel = StockHelper::getModelFromCode($request->kode_barang);

        if (!$stockModel) {
            return response()->json([
                'message' => 'Barang tidak ditemukan di stok.',
            ], 404);
        }

        // Menyimpan data ke Gudang In
        $gudangIn = new GudangIn($validated);
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

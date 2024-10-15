<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DayWork; // Contoh model, sesuaikan dengan stock

class StockController extends Controller
{
    public function index()
    {
        return view('stock');
    }

    public function store(Request $request)
    {
        $stock = new DayWork(); // Sesuaikan model yang Anda gunakan
        $stock->kode = $request->kode;
        $stock->nama_barang = $request->nama_barang;
        $stock->uty = $request->uty;
        $stock->satuan = $request->satuan;
        $stock->harga_satuan = $request->harga_satuan;
        $stock->stock_bahan = $request->stock_bahan;
        $stock->save();

        return redirect('/stock')->with('success', 'Data stock berhasil disimpan.');
    }
}


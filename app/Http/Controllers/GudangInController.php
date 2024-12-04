<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GudangIn;

class GudangInController extends Controller
{
    public function index()
    {
        return view('gudang_in');
    }

    public function store(Request $request)
    {
        $gudangIn = new GudangIn();
        $gudangIn->kode_barang = $request->kode_barang;
        $gudangIn->nama_barang = $request->nama_barang;
        $gudangIn->pengirim = $request->pengirim;
        $gudangIn->no_nota = $request->no_nota;
        $gudangIn->tanggal_barang_masuk = $request->tanggal_barang_masuk;
        $gudangIn->jumlah = $request->jumlah;
        $gudangIn->satuan = $request->satuan;
        $gudangIn->harga_satuan = $request->harga_satuan;
        $gudangIn->jumlah_harga = $request->jumlah_harga;
        $gudangIn->keterangan = $request->keterangan;
        $gudangIn->save();

        //return redirect('/gudang-in')->with('success', 'Data Gudang In berhasil disimpan.');
        // Mengembalikan respon dalam format JSON
        return response()->json([
            'message' => 'Data Gudang In berhasil disimpan.',
            'data' => $gudangIn
        ], 201);  // 201 status code untuk berhasil membuat data baru
    }
}


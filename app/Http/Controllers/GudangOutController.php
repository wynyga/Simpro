<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GudangOut;

class GudangOutController extends Controller
{
    public function index()
    {
        return view('gudang_out');
    }

    public function store(Request $request)
    {
        $gudangOut = new GudangOut();
        $gudangOut->kode_barang = $request->kode_barang;
        $gudangOut->nama_barang = $request->nama_barang;
        $gudangOut->tanggal = $request->tanggal;
        $gudangOut->peruntukan = $request->peruntukan;
        $gudangOut->jumlah = $request->jumlah;
        $gudangOut->satuan = $request->satuan;
        $gudangOut->jumlah_harga = $request->jumlah_harga;
        $gudangOut->keterangan = $request->keterangan;
        $gudangOut->save();

        if($request->wantsJson())
        {
            return response()->json([
                'message'=>'Data Gudang Out berhasil disimpan',
                'data'=>$gudangOut
            ],201);
        }
        else
        {
            return redirect('/gudang-out')->with('success', 'Data Gudang Out berhasil disimpan.');
         }
    }
}


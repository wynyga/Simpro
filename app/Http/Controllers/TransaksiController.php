<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BlokUnit;
use App\Models\UserPerumahan;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // Menampilkan daftar transaksi
    public function index()
    {
        $transaksi = Transaksi::with('blokUnit', 'userPerumahan')->get();
        return response()->json($transaksi);  // Mengembalikan data sebagai JSON
        // return view('transaksi.index', compact('transaksi'));
    }

    // Menampilkan form untuk menambahkan transaksi baru
    public function create()
    {
        $blok_units = BlokUnit::all();
        $users = UserPerumahan::all();
        return response()->json(['blok_units' => $blok_units, 'users' => $users]);
        // return view('transaksi.create', compact('blok_units', 'users'));
    }

    // Menyimpan transaksi baru ke database
    public function store(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'id_blok_unit' => 'required|exists:blok_unit,id',
            'id_user' => 'required|exists:user_perumahan,id',
            'harga_jual_standar' => 'required|numeric',
            'kelebihan_tanah' => 'nullable|numeric',
            'penambahan_luas_bangunan' => 'nullable|numeric',
            'perubahan_spek_bangunan' => 'nullable|numeric',
            'total_harga_jual' => 'required|numeric',
            'kpr_disetujui' => 'required|in:Ya,Tidak',
            'minimum_dp' => 'required|numeric',
            'kewajiban_hutang' => 'nullable|numeric',
        ]);

        // Membuat transaksi baru
        $transaksi=Transaksi::create($data);
        return response()->json([
            'message' => 'Transaksi berhasil ditambahkan',
            'data' => $transaksi
        ], 201);
        //return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    // Menampilkan form edit transaksi
    public function edit($id)
    {
        $transaksi = Transaksi::with('blokUnit', 'userPerumahan')->findOrFail($id);
        return response()->json($transaksi);
        //return view('transaksi.edit', compact('transaksi', 'blok_units', 'users'));
    }

    // Memperbarui transaksi yang ada
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // Validasi input
        $data = $request->validate([
            'id_blok_unit' => 'required|exists:blok_unit,id',
            'id_user' => 'required|exists:user_perumahan,id',
            'harga_jual_standar' => 'required|numeric',
            'kelebihan_tanah' => 'nullable|numeric',
            'penambahan_luas_bangunan' => 'nullable|numeric',
            'perubahan_spek_bangunan' => 'nullable|numeric',
            'total_harga_jual' => 'required|numeric',
            'kpr_disetujui' => 'required|in:Ya,Tidak',
            'minimum_dp' => 'required|numeric',
            'kewajiban_hutang' => 'nullable|numeric',
        ]);

        // Mengupdate transaksi
        $transaksi->update($data);
        return response()->json([
            'message' => 'Transaksi berhasil diperbarui',
            'data' => $transaksi
        ], 200);
        //return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    // Menghapus transaksi dari database
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();
        return response()->json([
            'message' => 'Transaksi berhasil dihapus'
        ], 200);
        //return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BlokUnit;
use App\Models\TipeRumah;
use Illuminate\Http\Request;

class BlokUnitController extends Controller
{
    // Menampilkan daftar blok unit
    public function index()
    {
        $blok_units = BlokUnit::with('tipeRumah.perumahan')->get();  // Mengambil blok unit dengan tipe rumah dan perumahan
        if ($blok_units->isEmpty()) {
            return response()->json(['message' => 'Tidak ada blok unit ditemukan'], 404);
        }
        return response()->json($blok_units);
        //return view('blokunit.index', compact('blok_units'));
    }

    public function create()
    {
        $tipe_rumah = TipeRumah::all();  // Mengambil semua data tipe rumah
        if ($tipe_rumah->isEmpty()) {
            return response()->json(['message' => 'Tipe rumah tidak ditemukan'], 404);
        }
        return response()->json($tipe_rumah);
        //return view('blokunit.create', compact('tipe_rumah'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'blok' => 'required',
            'unit' => 'required',
            'id_tipe_rumah' => 'required|exists:tipe_rumah,id',
        ]);

        $blok_unit=BlokUnit::create($data);  // Menyimpan data ke database
        return response()->json([
            'message'=>'Blok berhasil ditambahkan',
            'data'=>$blok_unit
        ],201);
    }
}

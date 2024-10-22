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
        return view('blokunit.index', compact('blok_units'));
    }

    public function create()
    {
        $tipe_rumah = TipeRumah::all();  // Mengambil semua data tipe rumah
        return view('blokunit.create', compact('tipe_rumah'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'blok' => 'required',
            'unit' => 'required',
            'id_tipe_rumah' => 'required|exists:tipe_rumah,id',
        ]);

        BlokUnit::create($data);  // Menyimpan data ke database
        return redirect()->route('blokunit.index')->with('success', 'Blok unit berhasil ditambahkan.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TipeRumah;
use App\Models\Perumahan;
use Illuminate\Http\Request;

class TipeRumahController extends Controller
{
    public function index()
    {
        $tipe_rumahs = TipeRumah::with('perumahan')->get();
        return view('tipe_rumah.index',compact('tipe_rumahs'));
    }

    public function create()
    {
        $perumahans = Perumahan::all();  // Mengambil semua data perumahan
        return view('tipe_rumah.create', compact('perumahans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_perumahan' => 'required|exists:perumahan,id',
            'tipe_rumah' => 'required|string|max:255',
            'luas_bangunan' => 'required|numeric',
            'luas_kavling' => 'required|numeric',
            'harga_standar_tengah' => 'required|numeric',
            'harga_standar_sudut' => 'required|numeric',
            'penambahan_bangunan' => 'required|numeric',
        ]);

        TipeRumah::create($data);
        return redirect()->route('tipe_rumah.index')->with('success', 'Tipe rumah berhasil ditambahkan.');
    }
}

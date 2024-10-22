<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use Illuminate\Http\Request;

class PerumahanController extends Controller
{
    public function index()
    {
        $perumahans = Perumahan::all();  // Mengambil semua data perumahan
        return view('perumahan.index', compact('perumahans'));  // Mengirim data ke view
    }
    
    public function create()
    {
        return view('perumahan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_perumahan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tanggal_harga' => 'required|date',
        ]);

        Perumahan::create($data);
        return redirect()->route('perumahan.index')->with('success', 'Perumahan berhasil ditambahkan.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Perumahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PerumahanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $perumahans = Perumahan::all(); 
        return response()->json($perumahans);
        //return view('perumahan.index', compact('perumahans'));  // Mengirim data ke view
    }
    
    // public function create()
    // {
    //     return view('perumahan.create');
    // }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_perumahan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tanggal_harga' => 'required|date',
        ]);

        $perumahan = Perumahan::create($data);
        return response()->json([
            'messages'=>'Perumahan berhasil ditambahkan',
            'data'=>$perumahan
        ]);
        //return redirect()->route('perumahan.index')->with('success', 'Perumahan berhasil ditambahkan.');
    }

    // Menambahkan fungsi untuk pemilihan perumahan
    public function selectPerumahan(Request $request)
    {
        $perumahanId = $request->input('perumahan_id');
        $perumahan = Perumahan::find($perumahanId);
        $user = auth()->user();  // Memastikan untuk mendapatkan user yang terautentikasi
    
        if ($perumahan && $user) {
            // Memperbarui session dan data user
            Session::put('perumahan_id', $perumahanId);
            $user->perumahan_id = $perumahanId;  // Menyimpan perumahan_id ke tabel user
            $user->save();
    
            Log::info('Perumahan ID set in session and user profile: ' . $perumahanId);
            return response()->json(['message' => 'Perumahan telah dipilih', 'perumahan_id' => $perumahanId]);
        } else {
            Log::error('Failed to find Perumahan with ID: ' . $perumahanId);
            return response()->json(['error' => 'Perumahan tidak ditemukan'], 404);
        }
    }

    // Fungsi untuk mengubah perumahan yang aktif
    public function changePerumahan(Request $request)
    {
        $newPerumahanId = $request->input('perumahan_id');
        $perumahan = Perumahan::find($newPerumahanId);
        $user = auth()->user();  // Memastikan untuk mendapatkan user yang terautentikasi
    
        if ($perumahan && $user) {
            // Memperbarui session dan data user
            Session::put('perumahan_id', $newPerumahanId);
            $user->perumahan_id = $newPerumahanId;  // Menyimpan perumahan_id ke tabel user
            $user->save();
    
            return response()->json([
                'message' => 'Perumahan telah berhasil diubah',
                'perumahan_id' => $newPerumahanId
            ]);
        } else {
            return response()->json(['error' => 'Perumahan tidak ditemukan'], 404);
        }
    }

}

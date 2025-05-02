<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Unit;
use App\Models\UserPerumahan;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
    
        $query = Transaksi::with('unit.blok', 'unit.tipeRumah', 'userPerumahan')
            ->where('perumahan_id', $user->perumahan_id);
    
        if ($search) {
            $query->whereHas('userPerumahan', function ($q) use ($search) {
                $q->where('nama_user', 'like', "%{$search}%");
            })->orWhereHas('unit', function ($q) use ($search) {
                $q->where('nomor_unit', 'like', "%{$search}%");
            });
        }
    
        return response()->json($query->paginate($perPage));
    }
    

    public function create()
    {
        $user = auth()->user();
        $unit = Unit::with('blok', 'tipeRumah')->whereHas('blok', function ($query) use ($user) {
            $query->where('perumahan_id', $user->perumahan_id);
        })->get();
        $users = UserPerumahan::where('perumahan_id', $user->perumahan_id)->get();
        return response()->json(['unit' => $unit, 'users' => $users]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'unit_id' => 'required|exists:unit,id',
            'user_id' => 'required|exists:user_perumahan,id',
            'harga_jual_standar' => 'required|numeric',
            'kelebihan_tanah' => 'nullable|numeric',
            'penambahan_luas_bangunan' => 'nullable|numeric',
            'perubahan_spek_bangunan' => 'nullable|numeric',
            'kpr_disetujui' => 'required|in:Ya,Tidak',
            'minimum_dp' => 'required|numeric',
            'biaya_booking' => 'nullable|numeric',
        ]);
    
        $data['kelebihan_tanah'] = $data['kelebihan_tanah'] ?? 0;
        $data['penambahan_luas_bangunan'] = $data['penambahan_luas_bangunan'] ?? 0;
        $data['perubahan_spek_bangunan'] = $data['perubahan_spek_bangunan'] ?? 0;
    
        $data['total_harga_jual'] = 
            $data['harga_jual_standar'] + 
            $data['kelebihan_tanah'] + 
            $data['penambahan_luas_bangunan'] + 
            $data['perubahan_spek_bangunan'];
    
        $data['plafon_kpr'] = $data['total_harga_jual'] - $data['minimum_dp'];
        $data['perumahan_id'] = $user->perumahan_id;
    
        $transaksi = Transaksi::create($data);
    
        return response()->json(['message' => 'Transaksi berhasil ditambahkan', 'data' => $transaksi], 201);
    }
    
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $transaksi = Transaksi::where('id', $id)
                              ->where('perumahan_id', $user->perumahan_id)
                              ->firstOrFail();
    
        $validated = $request->validate([
            'unit_id' => 'required|exists:unit,id',
            'user_id' => 'required|exists:user_perumahan,id',
            'harga_jual_standar' => 'required|numeric',
            'kelebihan_tanah' => 'nullable|numeric',
            'penambahan_luas_bangunan' => 'nullable|numeric',
            'perubahan_spek_bangunan' => 'nullable|numeric',
            'kpr_disetujui' => 'required|in:Ya,Tidak',
            'minimum_dp' => 'required|numeric',
            'biaya_booking' => 'nullable|numeric',
        ]);
    
        $validated['kelebihan_tanah'] = $validated['kelebihan_tanah'] ?? 0;
        $validated['penambahan_luas_bangunan'] = $validated['penambahan_luas_bangunan'] ?? 0;
        $validated['perubahan_spek_bangunan'] = $validated['perubahan_spek_bangunan'] ?? 0;
    
        $validated['total_harga_jual'] = 
            $validated['harga_jual_standar'] + 
            $validated['kelebihan_tanah'] + 
            $validated['penambahan_luas_bangunan'] + 
            $validated['perubahan_spek_bangunan'];
    
        $validated['plafon_kpr'] = $validated['total_harga_jual'] - $validated['minimum_dp'];
    
        $transaksi->update($validated);
    
        return response()->json(['message' => 'Transaksi berhasil diperbarui', 'data' => $transaksi], 200);
    }
    

    public function edit($id)
    {
        $user = auth()->user();
        $transaksi = Transaksi::with('unit.blok', 'unit.tipeRumah', 'userPerumahan')
                              ->where('id', $id)
                              ->where('perumahan_id', $user->perumahan_id)
                              ->firstOrFail();
        return response()->json($transaksi);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $transaksi = Transaksi::where('id', $id)
                              ->where('perumahan_id', $user->perumahan_id)
                              ->firstOrFail();
        $transaksi->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus'], 204);
    }
}

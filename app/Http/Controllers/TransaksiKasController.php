<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKas;

class TransaksiKasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Menampilkan indeks data transaksi kas dengan output JSON.
     */
    public function index()
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;  // Menggunakan perumahan_id dari profil user
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }
    
        // Menghitung total Cash In dan Cash Out berdasarkan perumahan_id
        $totalCashIn = TransaksiKas::where('kode', '101')->where('perumahan_id', $perumahanId)->sum('jumlah');
        $totalCashOut = TransaksiKas::where('kode', '102')->where('perumahan_id', $perumahanId)->sum('jumlah');
    
        // Menghitung saldo kas
        $saldoKas = $totalCashIn - $totalCashOut;
    
        // Mengambil semua data transaksi untuk ditampilkan
        $transaksiKas = TransaksiKas::where('perumahan_id', $perumahanId)->get();
    
        // Mengembalikan data sebagai JSON
        return response()->json([
            'totalCashIn' => $totalCashIn,
            'totalCashOut' => $totalCashOut,
            'saldoKas' => $saldoKas,
            'transaksiKas' => $transaksiKas
        ]);
    }
    

    /**
     * Menyimpan transaksi kas baru dengan output JSON.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
    
        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        // Validasi input
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'keterangan_transaksi' => 'required|string',
            'kode' => 'required|in:101,102', // Hanya menerima kode 101 atau 102
            'jumlah' => 'required|numeric',
            'keterangan_objek_transaksi' => 'nullable|string'
        ]);

        // Simpan transaksi baru
        $validated['perumahan_id'] = $perumahanId;  // Add perumahan_id from session
        $transaksi = TransaksiKas::create($validated);

        // Mengembalikan respons JSON
        return response()->json([
            'message' => 'Transaksi KAS berhasil disimpan.',
            'data' => $transaksi
        ], 201);
    }
}

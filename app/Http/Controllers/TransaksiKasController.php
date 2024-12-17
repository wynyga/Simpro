<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKas;

class TransaksiKasController extends Controller
{
    /**
     * Menampilkan indeks data transaksi kas dengan output JSON.
     */
    public function index()
    {
        // Menghitung total Cash In (Kode 101)
        $totalCashIn = TransaksiKas::where('kode', '101')->sum('jumlah');

        // Menghitung total Cash Out (Kode 102)
        $totalCashOut = TransaksiKas::where('kode', '102')->sum('jumlah');

        // Menghitung saldo kas (Cash In - Cash Out)
        $saldoKas = $totalCashIn - $totalCashOut;

        // Mengambil semua data transaksi untuk ditampilkan
        $transaksiKas = TransaksiKas::all();

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
        // Validasi input
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'keterangan_transaksi' => 'required|string',
            'kode' => 'required|in:101,102', // Hanya menerima kode 101 atau 102
            'jumlah' => 'required|numeric',
            'keterangan_objek_transaksi' => 'nullable|string'
        ]);

        // Simpan transaksi baru
        $transaksi = TransaksiKas::create($validated);

        // Mengembalikan respons JSON
        return response()->json([
            'message' => 'Transaksi KAS berhasil disimpan.',
            'data' => $transaksi
        ], 201);
    }
}

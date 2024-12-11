<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKas;

class TransaksiKasController extends Controller
{
    public function index()
    {   
        // Menghitung total Cash In (Kode 101)
        $totalCashIn = TransaksiKas::where('kode', '101')->sum('jumlah');

        // Menghitung total Cash Out (Kode 102)
        $totalCashOut = TransaksiKas::where('kode', '102')->sum('jumlah');

        // Menghitung saldo kas (Cash In - Cash Out)
        $saldoKas = $totalCashIn - $totalCashOut;

        // Mengambil semua data transaksi untuk ditampilkan di halaman
        $transaksiKas = TransaksiKas::all();
        // return view('transaksi_kas');
         return view('transaksi_kas', compact('totalCashIn', 'totalCashOut', 'saldoKas', 'transaksiKas'));
    }

    public function getTransaksiKasData()
    {
        $totalCashIn = TransaksiKas::where('kode', '101')->sum('jumlah');
        $totalCashOut = TransaksiKas::where('kode', '102')->sum('jumlah');
        $saldoKas = $totalCashIn - $totalCashOut;
        $transaksiKas = TransaksiKas::all();

        return response()->json([
            'totalCashIn' => $totalCashIn,
            'totalCashOut' => $totalCashOut,
            'saldoKas' => $saldoKas,
            'transaksiKas' => $transaksiKas
        ]);
    }

    public function store(Request $request)
    {
        // Validasi request
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan_transaksi' => 'required|string|max:255',
            'kode' => 'required|in:101,102',
            'jumlah' => 'required|numeric',
            'keterangan_objek_transaksi' => 'nullable|string|max:255'
        ]);

        // Simpan transaksi baru
        $transaksiKas = TransaksiKas::create([
            'tanggal' => $request->input('tanggal'),
            'keterangan_transaksi' => $request->input('keterangan_transaksi'),
            'kode' => $request->input('kode'),
            'jumlah' => $request->input('jumlah'),
            'keterangan_objek_transaksi' => $request->input('keterangan_objek_transaksi')
        ]);

        // Redirect ke halaman transaksi kas dengan pesan sukses
        // return redirect('/transaksi-kas')->with('success', 'Transaksi KAS berhasil disimpan.');
        return response()->json([
            'message' => 'Transaksi KAS berhasil disimpan.',
            'data' => $transaksiKas
        ], 201);
    }
}



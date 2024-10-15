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

        return view('transaksi_kas', compact('totalCashIn', 'totalCashOut', 'saldoKas', 'transaksiKas'));
    }

    public function store(Request $request)
    {
        // Simpan transaksi baru
        TransaksiKas::create([
            'tanggal' => $request->tanggal,
            'keterangan_transaksi' => $request->keterangan_transaksi,
            'kode' => $request->kode,
            'jumlah' => $request->jumlah,
            'keterangan_objek_transaksi' => $request->keterangan_objek_transaksi
        ]);

        return redirect('/transaksi-kas')->with('success', 'Transaksi KAS berhasil disimpan.');
    }
}



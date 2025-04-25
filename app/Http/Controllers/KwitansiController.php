<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use App\Models\TransaksiKas;
use App\Models\Perumahan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KwitansiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'transaksi_kas_id' => 'required|exists:transaksi_kas,id',
        ]);
    
        $transaksiKas = TransaksiKas::findOrFail($request->transaksi_kas_id);
    
        // Validasi status
        if ($transaksiKas->status !== 'approved') {
            return response()->json(['message' => 'Transaksi kas belum disetujui.'], 422);
        }
    
        // Cek apakah sudah pernah dibuat kwitansi untuk transaksi ini
        if (Kwitansi::where('transaksi_kas_id', $transaksiKas->id)->exists()) {
            return response()->json(['message' => 'Kwitansi untuk transaksi ini sudah ada.'], 409);
        }
    
        // Ambil data perumahan (pastikan relasi atau find by id)
        $perumahan = Perumahan::findOrFail($transaksiKas->perumahan_id);
    
        $latestId = Kwitansi::max('id') + 1;
        $no_doc = sprintf(
            "%02d/%s-%s/THN %d",
            $latestId,
            $transaksiKas->kode === '101' ? 'CI' : 'CO',
            $perumahan->inisial,
            now()->year
        );
    
        $kwitansi = Kwitansi::create([
            'transaksi_kas_id' => $transaksiKas->id,
            'perumahan_id' => $perumahan->id,
            'no_doc' => $no_doc,
            'tanggal' => now(),
            'dari' => $transaksiKas->dibuat_oleh,
            'jumlah' => $transaksiKas->jumlah,
            'untuk_pembayaran' => $transaksiKas->keterangan_objek_transaksi ?? $transaksiKas->keterangan_transaksi,
            'jenis_penerimaan' => $transaksiKas->metode_pembayaran,
            'dibuat_oleh' => auth()->user()->name,
            'disetor_oleh' => $transaksiKas->dibuat_oleh,
            'mengetahui' => null,
        ]);
    
        return response()->json($kwitansi);
    }
    

    public function show($id)
    {
        $kwitansi = Kwitansi::with(['transaksiKas', 'perumahan'])->findOrFail($id);
        return response()->json($kwitansi);
    }

    public function cetak($id)
    {
        $kwitansi = Kwitansi::with(['transaksiKas', 'perumahan'])->findOrFail($id);
    
        $pdf = Pdf::loadView('kwitansi.template', compact('kwitansi'))->setPaper('A5', 'portrait');
    
        // Ganti karakter / dan \ menjadi -
        $safeNoDoc = str_replace(['/', '\\'], '-', $kwitansi->no_doc);
    
        return $pdf->download("kwitansi-{$safeNoDoc}.pdf");
    }
    
}

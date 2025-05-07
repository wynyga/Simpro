<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use App\Models\TransaksiKas;
use App\Models\GudangIn;
use App\Models\Perumahan;
use App\Models\CostTee;
use App\Models\Transaksi;
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
    
        if ($transaksiKas->status !== 'approved') {
            return response()->json(['message' => 'Transaksi kas belum disetujui.'], 422);
        }
    
        if (Kwitansi::where('transaksi_kas_id', $transaksiKas->id)->exists()) {
            return response()->json(['message' => 'Kwitansi untuk transaksi ini sudah ada.'], 409);
        }
    
        $perumahan = Perumahan::findOrFail($transaksiKas->perumahan_id);
    
        $latestId = Kwitansi::max('id') + 1;
        $no_doc = sprintf(
            "%02d/%s-%s/THN %d",
            $latestId,
            $transaksiKas->kode === '101' ? 'CI' : 'CO',
            $perumahan->inisial,
            now()->year
        );
    
        $untukPembayaran = null;
    
        if ($transaksiKas->sumber_transaksi === 'cost_code') {
            $costTee = CostTee::find($transaksiKas->keterangan_transaksi_id);
            $untukPembayaran = $costTee ? "{$costTee->code} - {$costTee->description}" : "Tidak Diketahui";
        } elseif ($transaksiKas->sumber_transaksi === 'penjualan') {
            $transaksi = Transaksi::with('unit', 'userPerumahan')->find($transaksiKas->keterangan_transaksi_id);
            $untukPembayaran = $transaksi
                ? "Unit {$transaksi->unit->nomor_unit} - {$transaksi->userPerumahan->nama_user}"
                : "Tidak Diketahui";
        }
    
        $kwitansi = Kwitansi::create([
            'transaksi_kas_id' => $transaksiKas->id,
            'perumahan_id' => $perumahan->id,
            'no_doc' => $no_doc,
            'tanggal' => now(),
            'dari' => $transaksiKas->dibuat_oleh,
            'jumlah' => $transaksiKas->jumlah,
            'untuk_pembayaran' => $untukPembayaran,
            'metode_pembayaran' => $transaksiKas->metode_pembayaran,
            'dibuat_oleh' => auth()->user()->name,
            'disetor_oleh' => $transaksiKas->dibuat_oleh,
            'mengetahui' => null,
            'gudang_in_id' => null // hanya untuk transaksi kas, biarkan null
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

    public function cetakCO($id)
    {
        $kwitansi = Kwitansi::with(['gudangIn', 'perumahan'])
            ->where('id', $id)
            ->where('no_doc', 'like', '%/CO-%') // filter khusus kwitansi CO
            ->firstOrFail();
    
        $pdf = Pdf::loadView('kwitansi.template-co', compact('kwitansi'))->setPaper('A5', 'portrait');
        $safeNoDoc = str_replace(['/', '\\'], '-', $kwitansi->no_doc);
    
        return $pdf->download("kwitansi-co-{$safeNoDoc}.pdf");
    } 
}

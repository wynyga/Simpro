<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use App\Models\TransaksiKas;
use App\Models\Perumahan;
use App\Models\CostTee;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\KwitansiService;

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
        $kodeJenis = $transaksiKas->kode === '101' ? 'CI' : 'CO';

        $no_doc = KwitansiService::generateNoDoc($perumahan->id, $kodeJenis);

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
            'gudang_in_id' => null,
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
        $safeNoDoc = str_replace(['/', '\\'], '-', $kwitansi->no_doc);

        return $pdf->download("kwitansi-{$safeNoDoc}.pdf");
    }

    public function cetakCO($id)
    {
        $kwitansi = Kwitansi::with(['gudangIn', 'perumahan'])
            ->where('id', $id)
            ->where('no_doc', 'like', '%/CO-%')
            ->firstOrFail();

        $pdf = Pdf::loadView('kwitansi.template-co', compact('kwitansi'))->setPaper('A5', 'portrait');
        $safeNoDoc = str_replace(['/', '\\'], '-', $kwitansi->no_doc);

        return $pdf->download("kwitansi-co-{$safeNoDoc}.pdf");
    }

    public function all()
    {
        $user = auth()->user();

        $kwitansis = Kwitansi::with(['transaksiKas', 'perumahan'])
            ->where('perumahan_id', $user->perumahan_id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json($kwitansis);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Kwitansi::with(['transaksiKas', 'perumahan'])
            ->where('perumahan_id', $user->perumahan_id)
            ->orderBy('tanggal', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_doc', 'like', "%{$search}%")
                ->orWhere('untuk_pembayaran', 'like', "%{$search}%");
            });
        }

        $kwitansis = $query->paginate($perPage);

        return response()->json($kwitansis);
    }

}

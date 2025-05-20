<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiKas;

class PenjualanStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $filterStatus = $request->query('status'); // lunas, cicil, utang

        $transaksiList = Transaksi::where('perumahan_id', $perumahanId)
            ->with(['unit', 'userPerumahan'])
            ->get()
            ->map(function ($transaksi) {
                $totalBayar = TransaksiKas::where('sumber_transaksi', 'penjualan')
                    ->where('keterangan_transaksi_id', $transaksi->id)
                    ->where('status', 'approved')
                    ->sum('jumlah');

                // Penentuan target pembayaran berdasarkan jenis transaksi
                $isLunasLangsung = $transaksi->plafon_kpr == 0;
                $targetBayar = $isLunasLangsung
                    ? $transaksi->total_harga_jual
                    : $transaksi->plafon_kpr;

                $sisaHutang = max(0, $targetBayar - $totalBayar);

                // Status pembayaran
                if ($isLunasLangsung) {
                    $statusBayar = $totalBayar >= $transaksi->total_harga_jual ? 'lunas'
                        : ($totalBayar > 0 ? 'cicil' : 'utang');
                } else {
                    $statusBayar = $totalBayar >= $transaksi->plafon_kpr ? 'lunas'
                        : ($totalBayar > 0 ? 'cicil' : 'utang');
                }

                return [
                    'id' => $transaksi->id,
                    'unit_id' => $transaksi->unit_id,
                    'user_id' => $transaksi->user_id,
                    'total_harga_jual' => $transaksi->total_harga_jual,
                    'plafon_kpr' => $transaksi->plafon_kpr,
                    'minimum_dp' => $transaksi->minimum_dp,
                    'total_bayar' => (float) $totalBayar,
                    'sisa_hutang' => $sisaHutang,
                    'status_bayar' => $statusBayar,
                    'unit' => optional($transaksi->unit)->nomor_unit,
                    'pembeli' => optional($transaksi->userPerumahan)->nama_user,
                ];
            });

        // Filter jika ada query status
        if ($filterStatus) {
            $transaksiList = $transaksiList->where('status_bayar', $filterStatus)->values();
        }

        return response()->json($transaksiList);
    }
}

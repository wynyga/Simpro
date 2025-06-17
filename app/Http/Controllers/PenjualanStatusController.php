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

                $targetBayar = $transaksi->total_harga_jual; // bayar total, bukan hanya sisa
                $sisaHutang = max(0, $targetBayar - $totalBayar);

                $statusBayar = $totalBayar >= $targetBayar ? 'lunas'
                    : ($totalBayar > 0 ? 'cicil' : 'utang');


                return [
                    'id' => $transaksi->id,
                    'unit_id' => $transaksi->unit_id,
                    'user_id' => $transaksi->user_id,
                    'total_harga_jual' => $transaksi->total_harga_jual,
                    'minimum_dp' => $transaksi->minimum_dp,
                    'sisa_hutang_awal' => $transaksi->sisa_hutang,
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

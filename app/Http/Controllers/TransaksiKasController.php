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
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $totalCashIn = TransaksiKas::where('kode', '101')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved')
            ->sum('jumlah');

        $totalCashOut = TransaksiKas::where('kode', '102')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved')
            ->sum('jumlah');

        $saldoKas = $totalCashIn - $totalCashOut;

        $transaksiKas = TransaksiKas::where('perumahan_id', $perumahanId)->get()->map(function ($transaksi) {
            return [
                'id' => $transaksi->id,
                'tanggal' => $transaksi->tanggal,
                'kode' => $transaksi->kode,
                'jumlah' => $transaksi->jumlah ?? 0,
                'keterangan_objek_transaksi' => $transaksi->keterangan_objek_transaksi ?? "-",
                'metode_pembayaran' => $transaksi->metode_pembayaran ?? "Tunai",
                'saldo_setelah_transaksi' => $transaksi->saldo_setelah_transaksi ?? 0,
                'dibuat_oleh' => $transaksi->dibuat_oleh ?? "Admin",
                'status' => $transaksi->status,
                'sumber_transaksi' => $transaksi->sumber_transaksi ?? null,
                'keterangan_transaksi_id' => $transaksi->keterangan_transaksi_id ?? null,
            ];
        });

        return response()->json([
            'totalCashIn' => $totalCashIn,
            'totalCashOut' => $totalCashOut,
            'saldoKas' => $saldoKas,
            'transaksiKas' => $transaksiKas
        ]);
    }
    
    
    public function getJournalSummary($bulan, $tahun)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $totalDebit = TransaksiKas::where('kode', '101')
            ->where('perumahan_id', $perumahanId)
            ->whereYear('tanggal', '<=', $tahun)
            ->whereMonth('tanggal', '<=', $bulan)
            ->sum('jumlah');

        $totalKredit = TransaksiKas::where('kode', '102')
            ->where('perumahan_id', $perumahanId)
            ->whereYear('tanggal', '<=', $tahun)
            ->whereMonth('tanggal', '<=', $bulan)
            ->sum('jumlah');

        $saldo = $totalDebit - $totalKredit;

        return response()->json([
            'sheet_balance' => [
                'total_rp' => round($saldo, 2),
                'color' => $saldo >= 0 ? 'green' : 'red'
            ],
            'debit' => round($totalDebit, 2),
            'kredit' => round($totalKredit, 2),
            'saldo' => round($saldo, 2)
        ]);
    }
    

    public function store(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kode' => 'required|in:101,102',
            'jumlah' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:Tunai,Transfer Bank,Cek,Giro,Draft',
            'sumber_transaksi' => 'required|in:cost_code,penjualan',
            'keterangan_transaksi_id' => 'required|numeric', // sekarang ini yang wajib
        ]);

        $transaksi = TransaksiKas::create([
            'tanggal' => $validated['tanggal'],
            'kode' => $validated['kode'],
            'jumlah' => $validated['jumlah'],
            'saldo_setelah_transaksi' => null,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'dibuat_oleh' => $user->name,
            'keterangan_objek_transaksi' => $validated['keterangan_objek_transaksi'] ?? null,
            'perumahan_id' => $perumahanId,
            'status' => 'pending',
            'sumber_transaksi' => $validated['sumber_transaksi'],
            'keterangan_transaksi_id' => $validated['keterangan_transaksi_id'],

        ]);
        

        return response()->json([
            'message' => 'Transaksi KAS berhasil disimpan dan menunggu persetujuan.',
            'data' => $transaksi
        ], 201);
    }
    

    public function approveTransaction($id)
    {
        $transaksi = TransaksiKas::find($id);

        if (!$transaksi) {
            return response()->json(['error' => 'Transaksi tidak ditemukan.'], 404);
        }

        if ($transaksi->status !== 'pending') {
            return response()->json(['error' => 'Transaksi sudah diproses sebelumnya.'], 400);
        }

        $totalCashIn = TransaksiKas::where('kode', '101')->where('status', 'approved')->sum('jumlah');
        $totalCashOut = TransaksiKas::where('kode', '102')->where('status', 'approved')->sum('jumlah');
        $saldoSebelumnya = $totalCashIn - $totalCashOut;

        $saldoSetelahTransaksi = ($transaksi->kode === '101')
            ? $saldoSebelumnya + $transaksi->jumlah
            : $saldoSebelumnya - $transaksi->jumlah;

        $transaksi->update([
            'status' => 'approved',
            'saldo_setelah_transaksi' => $saldoSetelahTransaksi
        ]);

        return response()->json(['message' => 'Transaksi berhasil disetujui.', 'data' => $transaksi]);
    }
    
    public function rejectTransaction($id)
    {
        $transaksi = TransaksiKas::find($id);

        if (!$transaksi) {
            return response()->json(['error' => 'Transaksi tidak ditemukan.'], 404);
        }

        if ($transaksi->status !== 'pending') {
            return response()->json(['error' => 'Transaksi sudah diproses sebelumnya.'], 400);
        }

        $transaksi->update(['status' => 'rejected']);

        return response()->json(['message' => 'Transaksi berhasil ditolak.', 'data' => $transaksi]);
    }

    public function getHistory(Request $request)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $perPage = $request->query('per_page', 10);

        $query = TransaksiKas::with('kwitansi')->where('perumahan_id', $perumahanId);

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $transaksiKas = $query->orderBy('tanggal', 'desc')->paginate($perPage);

        return response()->json($transaksiKas);
    }
    

    public function getRingkasanKasPerTahun(Request $request, $tahun = null)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;
        $tahun = $tahun ?? date('Y');

        if (empty($perumahanId)) {
            return response()->json(['error' => 'User does not have a perumahan_id.'], 403);
        }

        $totalCashIn = TransaksiKas::where('kode', '101')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved')
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        $totalCashOut = TransaksiKas::where('kode', '102')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved')
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        $saldoKas = $totalCashIn - $totalCashOut;

        $transaksiKas = TransaksiKas::where('perumahan_id', $perumahanId)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'approved')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($transaksi) {
                return [
                    'id' => $transaksi->id,
                    'tanggal' => $transaksi->tanggal,
                    'kode' => $transaksi->kode,
                    'jumlah' => number_format($transaksi->jumlah, 2, '.', ''),
                    'keterangan_objek_transaksi' => $transaksi->keterangan_objek_transaksi ?? "-",
                    'metode_pembayaran' => $transaksi->metode_pembayaran ?? "Tunai",
                    'saldo_setelah_transaksi' => number_format($transaksi->saldo_setelah_transaksi ?? 0, 2, '.', ''),
                    'dibuat_oleh' => $transaksi->dibuat_oleh ?? "Admin",
                    'status' => $transaksi->status,
                    'sumber_transaksi' => $transaksi->sumber_transaksi ?? null,
                    'keterangan_transaksi_id' => $transaksi->keterangan_transaksi_id ?? null,
                ];
            });

        return response()->json([
            'tahun' => (string) $tahun,
            'totalCashIn' => number_format($totalCashIn, 2, '.', ''),
            'totalCashOut' => number_format($totalCashOut, 2, '.', ''),
            'saldoKas' => number_format($saldoKas, 2, '.', ''),
            'transaksiKas' => $transaksiKas,
        ]);
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKas;
use App\Models\LapBulanan;
use Illuminate\Support\Facades\DB;

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

        // Hitung total kas masuk (101) dan keluar (102) yang sudah disetujui
        $totalCashIn = TransaksiKas::where('kode', '101')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved')
            ->sum('jumlah');

        $totalCashOut = TransaksiKas::where('kode', '102')
            ->where('perumahan_id', $perumahanId)
            ->where('status', 'approved')
            ->sum('jumlah');

        $saldoKas = $totalCashIn - $totalCashOut;

        // Ambil transaksi dengan relasi ke cost_tees
        $transaksiKas = TransaksiKas::with('costTee')
            ->where('perumahan_id', $perumahanId)
            ->get()
            ->map(function ($transaksi) {
                return [
                    'id' => $transaksi->id,
                    'tanggal' => $transaksi->tanggal,
                    'kode' => $transaksi->kode,
                    'jumlah' => $transaksi->jumlah ?? 0,
                    'keterangan_transaksi' => $transaksi->costTee->description ?? '-', // Ambil dari relasi cost_tees
                    'metode_pembayaran' => $transaksi->metode_pembayaran ?? "Cash",
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
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', '<=', $bulan)
            ->sum('jumlah');

        $totalKredit = TransaksiKas::where('kode', '102')
            ->where('perumahan_id', $perumahanId)
            ->whereYear('tanggal', $tahun)
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
            'metode_pembayaran' => 'required|in:Cash,Transfer Bank,Cek,Giro,Draft',
            'sumber_transaksi' => 'required|in:cost_code,penjualan',
            'keterangan_transaksi_id' => 'required|numeric',
            'keterangan_objek_transaksi' => 'nullable|string',
        ]);

        // Tentukan default keterangan_objek_transaksi jika tidak diisi dan sumbernya cost_code
        $keteranganObjek = $validated['keterangan_objek_transaksi'] ?? null;

        if (!$keteranganObjek && $validated['sumber_transaksi'] === 'cost_code') {
            $costTee = \App\Models\CostTee::find($validated['keterangan_transaksi_id']);
            if ($costTee) {
                $jenisLabel = $validated['kode'] === '101' ? 'Penerimaan' : 'Pengeluaran';
                $keteranganObjek = "{$jenisLabel} - {$costTee->description}";
            }
        }

        // Simpan transaksi kas
        $transaksi = \App\Models\TransaksiKas::create([
            'tanggal' => $validated['tanggal'],
            'kode' => $validated['kode'],
            'jumlah' => $validated['jumlah'],
            'saldo_setelah_transaksi' => null,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'dibuat_oleh' => $user->name,
            'keterangan_objek_transaksi' => $keteranganObjek,
            'perumahan_id' => $perumahanId,
            'status' => 'pending',
            'sumber_transaksi' => $validated['sumber_transaksi'],
            'keterangan_transaksi_id' => $validated['keterangan_transaksi_id'],
            'jenis_transaksi' => $validated['kode'] === '101' ? 'KASIN' : 'KASOUT',
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

        // Hitung saldo sebelumnya
        $totalCashIn = TransaksiKas::where('kode', '101')->where('status', 'approved')->sum('jumlah');
        $totalCashOut = TransaksiKas::where('kode', '102')->where('status', 'approved')->sum('jumlah');
        $saldoSebelumnya = $totalCashIn - $totalCashOut;

        // Hitung saldo setelah transaksi ini
        $saldoSetelahTransaksi = ($transaksi->kode === '101')
            ? $saldoSebelumnya + $transaksi->jumlah
            : $saldoSebelumnya - $transaksi->jumlah;

        // Update status transaksi kas
        $transaksi->update([
            'status' => 'approved',
            'saldo_setelah_transaksi' => $saldoSetelahTransaksi
        ]);

        // === Auto-insert/update ke laporan bulanan jika dari cost_code
        if ($transaksi->sumber_transaksi === 'cost_code') {
            $bulan = (int) date('n', strtotime($transaksi->tanggal));
            $tahun = (int) date('Y', strtotime($transaksi->tanggal));
            $jenisTransaksi = $transaksi->kode === '101' ? 'KASIN' : 'KASOUT';

            // Cek apakah sudah ada entri lap_bulanan
            $lap = LapBulanan::firstOrNew([
                'perumahan_id' => $transaksi->perumahan_id,
                'cost_tee_id' => $transaksi->keterangan_transaksi_id,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);

            // Update field dan jumlah akumulatif
            $lap->jenis_transaksi = $jenisTransaksi;
            $lap->code_account = $transaksi->kode;
            $lap->jumlah = ($lap->exists ? $lap->jumlah : 0) + $transaksi->jumlah;
            $lap->save();
        }

        return response()->json([
            'message' => 'Transaksi berhasil disetujui.',
            'data' => $transaksi
        ]);
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
                    'metode_pembayaran' => $transaksi->metode_pembayaran ?? "Cash",
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

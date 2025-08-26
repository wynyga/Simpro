<?php

namespace App\Http\Controllers;

use App\Models\LapBulanan;
use App\Models\CostTee;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;

class LapBulananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $laporans = LapBulanan::where('perumahan_id', $user->perumahan_id)
            ->with('costTee.costElement.costCentre')
            ->get();

        if ($laporans->isEmpty()) {
            return response()->json(['message' => 'Tidak ada laporan bulanan ditemukan'], 404);
        }

        return response()->json($laporans);
    }

    public function getKasMasuk($bulan, $tahun)
    {
        $user = auth()->user();

        $totalKasMasuk = LapBulanan::whereHas('costTee.costElement.costCentre', function ($q) use ($user) {
            $q->where('cost_code', 'KASIN')->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulan)->where('tahun', $tahun)->sum('jumlah');

        // Hitung saldo bulan sebelumnya
        $bulanSebelumnya = $bulan == 1 ? 12 : $bulan - 1;
        $tahunSebelumnya = $bulan == 1 ? $tahun - 1 : $tahun;

        $saldoKasSebelumnya = LapBulanan::whereHas('costTee.costElement.costCentre', function ($q) use ($user) {
            $q->where('cost_code', 'KASIN')->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulanSebelumnya)->where('tahun', $tahunSebelumnya)->sum('jumlah');

        return response()->json([
            'saldo_kas_sebelumnya' => [
                'code_account' => "B{$bulan}{$tahun}",
                'total_rp' => number_format($saldoKasSebelumnya ?? 0, 2, ',', '.')
            ],
            'penerimaan_kas_bulan_ini' => [
                'code_account' => null,
                'total_rp' => number_format($totalKasMasuk ?? 0, 2, ',', '.')
            ],
            'total_kas_project' => [
                'total_rp' => number_format(($saldoKasSebelumnya + $totalKasMasuk) ?? 0, 2, ',', '.')
            ]
        ]);
    }

    public function getKasKeluar($bulan, $tahun)
    {
        $user = auth()->user();

        $totalKasKeluar = LapBulanan::whereHas('costTee.costElement.costCentre', function ($q) use ($user) {
            $q->where('cost_code', 'KASOUT')->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulan)->where('tahun', $tahun)->sum('jumlah');

        $laporanKasKeluar = LapBulanan::with('costTee.costElement.costCentre')
            ->whereHas('costTee.costElement.costCentre', function ($q) use ($user) {
                $q->where('cost_code', 'KASOUT')->where('perumahan_id', $user->perumahan_id);
            })
            ->where('bulan', $bulan)->where('tahun', $tahun)->get();

        return response()->json([
            'total_kas_keluar' => [
                'total_rp' => number_format($totalKasKeluar ?? 0, 2, ',', '.')
            ],
            'rincian_kas_keluar' => $laporanKasKeluar->map(function ($laporan) {
                return [
                    'id' => $laporan->id,
                    'code_account' => $laporan->code_account,
                    'kategori' => optional($laporan->costTee)->description,
                    'jumlah' => number_format($laporan->jumlah, 2, ',', '.'),
                    'created_at' => $laporan->created_at
                ];
            })
        ]);
    }

    public function getSisaKasProject($bulan, $tahun)
    {
        $user = auth()->user();

        $masuk = LapBulanan::whereHas('costTee.costElement.costCentre', function ($q) use ($user) {
            $q->where('cost_code', 'KASIN')->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulan)->where('tahun', $tahun)->sum('jumlah');

        $keluar = LapBulanan::whereHas('costTee.costElement.costCentre', function ($q) use ($user) {
            $q->where('cost_code', 'KASOUT')->where('perumahan_id', $user->perumahan_id);
        })->where('bulan', $bulan)->where('tahun', $tahun)->sum('jumlah');

        $sisa = $masuk - $keluar;

        return response()->json([
            'sisa_kas_project' => [
                'total_rp' => number_format($sisa, 2, ',', '.'),
                'status' => $sisa < 0 ? 'DEFISIT' : 'SURPLUS'
            ]
        ]);
    }

    public function getHistory(Request $request)
    {
        $user = auth()->user();
        $query = LapBulanan::where('perumahan_id', $user->perumahan_id)
            ->with('costTee.costElement.costCentre');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->bulan) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        $perPage = $request->query('per_page', 10);
        return response()->json(
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->paginate($perPage)
        );
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'cost_tee_id' => 'required|exists:cost_tees,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'jumlah' => 'required|numeric'
        ]);

        $costTee = CostTee::where('id', $validated['cost_tee_id'])
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costTee) {
            return response()->json(['error' => 'Unauthorized: Cost Tee bukan milik perumahan Anda.'], 403);
        }

        $validated['perumahan_id'] = $user->perumahan_id;

        $laporan = LapBulanan::create($validated);

        return response()->json([
            'message' => 'Laporan bulanan berhasil disimpan',
            'data' => $laporan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $laporan = LapBulanan::where('id', $id)->where('perumahan_id', $user->perumahan_id)->first();

        if (!$laporan) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk mengupdate laporan ini.'], 403);
        }

        $validated = $request->validate([
            'cost_tee_id' => 'required|exists:cost_tees,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'jumlah' => 'required|numeric'
        ]);

        $costTee = CostTee::where('id', $validated['cost_tee_id'])
            ->where('perumahan_id', $user->perumahan_id)
            ->first();

        if (!$costTee) {
            return response()->json(['error' => 'Unauthorized: Cost Tee bukan milik perumahan Anda.'], 403);
        }

        $laporan->update($validated);

        return response()->json([
            'message' => 'Laporan bulanan berhasil diperbarui',
            'data' => $laporan
        ], 200);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $laporan = LapBulanan::where('id', $id)->where('perumahan_id', $user->perumahan_id)->first();

        if (!$laporan) {
            return response()->json(['error' => 'Unauthorized: Anda tidak memiliki akses untuk menghapus laporan ini.'], 403);
        }

        $laporan->delete();

        return response()->json(['message' => 'Laporan bulanan berhasil dihapus'], 204);
    }

    public function getLaporanTahunan($tahun)
    {
        $user = auth()->user();

        $laporans = LapBulanan::with('costTee.costElement.costCentre')
            ->where('perumahan_id', $user->perumahan_id)
            ->where('tahun', $tahun)
            ->get();

        $totalKasMasuk = $laporans->where('jenis_transaksi', 'KASIN')->sum('jumlah');
        $totalKasKeluar = $laporans->where('jenis_transaksi', 'KASOUT')->sum('jumlah');
        $sisaKas = $totalKasMasuk - $totalKasKeluar;

        $rekap = $laporans->groupBy('bulan')->map(function ($bulanItems, $bulan) {
            return [
                'bulan' => (int) $bulan,
                'item' => $bulanItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'bulan' => $item->bulan,
                        'tahun' => $item->tahun,
                        'jumlah' => number_format($item->jumlah, 2, ',', '.'),
                        'jumlah_raw' => $item->jumlah, 
                        'code_account' => $item->code_account,
                        'kategori' => optional($item->costTee)->description,
                        'cost_code' => optional($item->costTee->costElement->costCentre)->cost_code,
                        'jenis_transaksi' => $item->jenis_transaksi,
                    ];
                })->values()
            ];
        })->values();

        return response()->json([
            'tahun' => $tahun,
            'total_kas_masuk' => [
                'total_rp' => number_format($totalKasMasuk, 2, ',', '.'),
                'raw' => (float) $totalKasMasuk,
            ],
            'total_kas_keluar' => [
                'total_rp' => number_format($totalKasKeluar, 2, ',', '.'),
                'raw' => (float) $totalKasKeluar,
            ],
            'sisa_kas' => [
                'total_rp' => number_format($sisaKas, 2, ',', '.'),
                'raw' => (float) $sisaKas,
                'status' => $sisaKas < 0 ? 'DEFISIT' : 'SURPLUS',
            ],
            'rekap_detail' => $rekap,
        ]);
    }

    public function getByBulanTahun($bulan, $tahun)
    {
        $user = auth()->user();

        $laporans = LapBulanan::with('costTee.costElement.costCentre')
            ->where('perumahan_id', $user->perumahan_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        return response()->json($laporans);
    }

    public function getLaporanKas($bulan, $tahun)
    {
        $user = auth()->user();
        $perumahan = $user->perumahan;

        if (!$perumahan) {
            return response()->json(['error' => 'Perumahan tidak ditemukan untuk pengguna ini.'], 404);
        }

        // Hitung saldo awal (saldo kumulatif dari semua bulan sebelum bulan yang diminta)
        $startingBalance = LapBulanan::where('perumahan_id', $user->perumahan_id)
            ->where(function ($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                    ->orWhere(function ($q) use ($tahun, $bulan) {
                        $q->where('tahun', $tahun)
                            ->where('bulan', '<', $bulan);
                    });
            })
            ->get()
            ->sum(function ($item) {
                $isKasIn = optional($item->costTee->costElement->costCentre)->cost_code == 'KASIN';
                return $isKasIn ? $item->jumlah : -$item->jumlah;
            });

        // Ambil semua transaksi di bulan dan tahun yang diminta
        $transactions = LapBulanan::with('costTee.costElement.costCentre')
            ->where('perumahan_id', $user->perumahan_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        // Inisialisasi variabel untuk ringkasan laporan
        $totalTransactionDebitCount = 0;
        $totalTransactionCreditCount = 0;
        $totalTransactionDebitAmount = 0;
        $totalTransactionCreditAmount = 0;
        
        $currentBalance = $startingBalance;
        $formattedTransactions = [];

        // Iterasi untuk memformat data, menghitung jumlah & nilai transaksi, serta saldo berjalan
        foreach ($transactions as $index => $item) {
            $debit = 0;
            $credit = 0;
            
            $costCode = optional($item->costTee->costElement->costCentre)->cost_code;

            if ($costCode == 'KASOUT') {
                $debit = $item->jumlah;
                $totalTransactionDebitCount++;
                $totalTransactionDebitAmount += $debit;
            } elseif ($costCode == 'KASIN') {
                $credit = $item->jumlah;
                $totalTransactionCreditCount++;
                $totalTransactionCreditAmount += $credit;
            }

            $currentBalance += ($credit - $debit);

            $formattedTransactions[] = [
                'no' => $index + 1,
                'postingDate' => $item->created_at->format('d/m/Y'),
                'postingTime' => $item->created_at->format('H:i:s'),
                'effDate' => $item->created_at->format('d/m/Y'),
                'effTime' => $item->created_at->format('H:i:s'),
                'description' => optional($item->costTee)->description,
                'debit' => 0,
                'credit' => 0,
                'balance' => 0
            ];
        }
        
        // Saldo akhir adalah saldo berjalan terakhir dari loop
        $endingBalance = $currentBalance;

        return response()->json([
            'company' => 'PT BUMI ASIH',
            'accountOrganizationUnit' => optional($perumahan)->nama_perumahan,
            'period' => "Bulan " . $bulan . " Tahun " . $tahun,
            'startingBalance' => 0,
            'endingBalance' => 0,
            'totalTransactionDebit' => $totalTransactionDebitCount, 
            'totalTransactionCredit' => $totalTransactionCreditCount,
            'transactions' => $formattedTransactions
        ]);
    }
}

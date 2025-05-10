<?php

namespace App\Http\Controllers;

use App\Models\LapBulanan;
use App\Models\CostTee;
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
    
        $rekap = [];
    
        foreach ($laporans as $laporan) {
            $bulan = $laporan->bulan;
            $kategori = optional($laporan->costTee)->description ?? 'Tidak Diketahui';
            $jenis = optional($laporan->costTee->costElement->costCentre)->cost_code ?? '-';
            $jumlah = $laporan->jumlah;
    
            $rekap[$bulan][] = [
                'kategori' => $kategori,
                'jenis' => $jenis,
                'jumlah' => $jumlah,
            ];
        }
    
        $totalKasMasuk = $laporans->filter(function ($laporan) {
            return optional($laporan->costTee->costElement->costCentre)->cost_code === 'KASIN';
        })->sum('jumlah');
    
        $totalKasKeluar = $laporans->filter(function ($laporan) {
            return optional($laporan->costTee->costElement->costCentre)->cost_code === 'KASOUT';
        })->sum('jumlah');
    
        $sisaKas = $totalKasMasuk - $totalKasKeluar;
    
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

    
}

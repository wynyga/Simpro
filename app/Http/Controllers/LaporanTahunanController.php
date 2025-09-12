<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CostCentre;
use App\Models\CostElement;
use App\Models\CostTee;
use App\Models\TransaksiKas;

class LaporanTahunanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getLaporanTahunan($tahun)
    {
        $user = auth()->user();
        $perumahanId = $user->perumahan_id;

        // ambil semua cost centre user
        $costCentres = CostCentre::where('perumahan_id', $perumahanId)
            ->with(['costElements.costTees'])
            ->get();

        $result = [];

        foreach ($costCentres as $centre) {
            $centreData = [
                'cost_centre_code' => $centre->cost_centre_code,
                'description' => $centre->description,
                'total' => 0,
                'elements' => []
            ];

            foreach ($centre->costElements as $element) {
                $elementData = [
                    'cost_element_code' => $element->cost_element_code,
                    'description' => $element->description,
                    'total' => 0,
                    'tees' => []
                ];

                foreach ($element->costTees as $tee) {
                    // hitung jumlah transaksi kas tahunan untuk cost tee ini
                    $jumlah = TransaksiKas::where('perumahan_id', $perumahanId)
                        ->where('sumber_transaksi', 'cost_code')
                        ->where('keterangan_transaksi_id', $tee->id) // relasi ke cost tee
                        ->whereYear('tanggal', $tahun)
                        ->sum('jumlah');

                    $elementData['tees'][] = [
                        'cost_tee_code' => $tee->cost_tee_code,
                        'description' => $tee->description,
                        'jumlah' => $jumlah
                    ];

                    $elementData['total'] += $jumlah;
                }

                $centreData['elements'][] = $elementData;
                $centreData['total'] += $elementData['total'];
            }

            // kelompokkan berdasarkan KASIN / KASOUT
            $result[$centre->cost_code][] = $centreData;
        }

        return response()->json($result, 200);
    }
}

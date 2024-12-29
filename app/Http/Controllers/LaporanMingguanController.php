<?php

namespace App\Http\Controllers;

use App\Models\LaporanMingguan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LaporanMingguanController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'minggu_ke' => 'required|integer',
            'tahun_ke' => 'required|integer',
            'jenis_biaya' => 'required|string',
            'uraian' => 'sometimes|nullable|string',
            'kategori' => 'sometimes|nullable|string',
            'sub_kategori' => 'sometimes|nullable|string',
            'sub_subkategori'=> 'sometimes|nullable|string',
            'total' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $code = LaporanMingguan::generateCode($request->minggu_ke, $request->tahun_ke);
        $code_account = $this->generateCode($request->jenis_biaya,$request->sub_kategori, $code);
        // Cek duplikasi code_account di database
        $existingCodeAccount = LaporanMingguan::where('code_account', $code_account)->first();
        if ($existingCodeAccount) {
            return response()->json(['error' => 'Code account duplikat tidak diperbolehkan'], 409);
        }   
        
        // Cek duplikasi data
        $existingReport = LaporanMingguan::where('jenis_biaya', $request->jenis_biaya)
                                        ->where('minggu_ke', $request->minggu_ke)
                                        ->where('tahun_ke', $request->tahun_ke)
                                        ->where('uraian', $request->uraian)
                                        ->where('kategori', $request->kategori)
                                        ->where('sub_kategori', $request->sub_kategori)
                                        ->where('sub_subkategori',$request->sub_subkategori)    
                                        ->first();

        if ($existingReport) {
            return response()->json(['error' => 'Data duplikat tidak diperbolehkan'], 409);
        }

        $laporan = new LaporanMingguan([
            'minggu_ke' => $request->minggu_ke,
            'tahun_ke' => $request->tahun_ke,
            'jenis_biaya' => $request->jenis_biaya,
            'uraian' => $request->uraian,
            'kategori' => $request->kategori,
            'sub_kategori' => $request->sub_kategori,
            'sub_subkategori'=>$request->sub_subkategori,
            'code_account' => $code_account,
            'code' => $code, // Memastikan code diisi
            'total' => $request->total
        ]);
        $laporan->save();

        return response()->json([
            'message' => 'Laporan mingguan berhasil ditambahkan',
            'data' => $laporan
        ], 201);
    }    

    protected function generateCode($jenis_biaya, $sub_kategori, $weekCode)
    {
        $prefix = ''; // Inisialisasi prefix
        $number = '';
        $number2 = '';
        
        if ($jenis_biaya == 'KAS PROJECT / KAS MASUK MINGGU INI') {
            $prefix = 'KI';
            
            // Hitung berapa banyak laporan yang sudah ada dengan jenis biaya dan sub_kategori yang sama
            $count = LaporanMingguan::count();
    
            // Format nomor urutan untuk memastikan selalu dua digit
            $number = sprintf('%02d', $count + 1);
            return $prefix  . $number . $weekCode;
    
        } elseif ($jenis_biaya == 'KAS KELUAR MINGGU INI') {
            $prefix = 'KO';
    
            // Sama seperti di atas, hitung untuk KAS KELUAR
            $count2 = LaporanMingguan::count();
    
            // Format nomor urutan untuk memastikan selalu dua digit
            $number2 = sprintf('%02d', $count2 + 1);
            return $prefix  . $number2 . $weekCode;
    
        } else {
            // Jika jenis biaya tidak sesuai dengan dua kondisi di atas, kembalikan null atau prefix default lain.
            return null;
        }
    }
    
    

}

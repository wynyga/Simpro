<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanMingguan;

class LaporanMingguanController extends Controller
{
    // Method untuk menampilkan form input laporan mingguan
    public function create()
    {
        // Menampilkan halaman form input laporan mingguan
        return view('laporan_mingguan.create');
    }
    public function index()
    {
        // Mengambil semua data laporan mingguan dari database
        $laporan = LaporanMingguan::all();

        // Mengirimkan data laporan ke view 'laporan_mingguan.index'
        return view('laporan_mingguan.index', compact('laporan'));
    }
    public function store(Request $request)
    {
        //dd($request->all());
        // Validasi input
        $request->validate([
            'minggu_ke' => 'required|integer',
            'tahun_ke' => 'required|integer',
            'jenis_biaya' => 'required|string',
            'uraian' => 'required|string',
            'kategori' => 'sometimes|string|nullable',
            'sub_kategori' => 'sometimes|string|nullable',
            'total' => 'required|numeric',  // Sesuaikan dengan input dari form
            'deskripsi' => 'nullable|string',
        ]);
    
        // Generate kode berdasarkan minggu dan tahun
        $code = LaporanMingguan::generateCode($request->minggu_ke, $request->tahun_ke);
    
        // Tentukan uraian, kategori, sub_kategori, code_account, dan jenis_biaya
        list($uraian, $kategori, $sub_kategori, $code_account) = $this->getUraianKategoriSubKategoriAndCodeAccount($request);
        //dd($uraian, $kategori, $sub_kategori, $code_account);
        // Simpan data transaksi
        LaporanMingguan::create([
            'minggu_ke' => $request->minggu_ke,
            'tahun_ke' => $request->tahun_ke,
            'code' => $code,
            'jenis_biaya' => $request->jenis_biaya,  // Jenis Biaya baru
            'uraian' => $uraian,  // Gabungan Uraian > Kategori > Sub_Kategori
            'kategori' => $kategori,  // Kategori
            'sub_kategori' => $sub_kategori,  // Sub_Kategori
            'code_account' => $code_account,  // Kode Account berdasarkan uraian
            'total' => $request->total,  // Gunakan 'total' yang sesuai dengan form
            'deskripsi' => $request->deskripsi,  // Gunakan 'deskripsi' sesuai form
        ]);
    
        return redirect()->route('laporan_mingguan.index')->with('success', 'Laporan Mingguan berhasil disimpan');
    }
    

    // Method untuk menentukan Uraian, Kategori, Sub_Kategori, dan Code Account

    private function getUraianKategoriSubKategoriAndCodeAccount(Request $request)
    {
        $mingguSebelumnya = $request->minggu_ke - 1;
        $tahun = $request->tahun_ke;
    
        // Tentukan uraian, kategori, sub_kategori, dan code_account berdasarkan pilihan user
        switch ($request->jenis_biaya){
            case 'KAS PROJECT / KAS MASUK MINGGU INI':
                switch ($request->uraian) {
                    case 'saldo_sisa':
                        return [
                            "Saldo sisa Kas Proyek Minggu sebelumnya", 
                            null,  // Tidak ada kategori
                            null,  // Tidak ada sub_kategori
                            "M{$mingguSebelumnya}{$tahun}"  // Code Account
                        ];
                    case 'penerimaan_kas':
                        switch ($request->kategori) {
                            case 'operasional_proyek':
                                if ($request->sub_kategori === 'booking_fee') {
                                    return [
                                        "Penerimaan Booking Fee", 
                                        "Penerimaan dari Operasional Proyek",  // Kategori
                                        "Booking Fee",  // Sub Kategori
                                        "KI0101M{$request->minggu_ke}{$tahun}"  // Code Account
                                    ];
                                } elseif ($request->sub_kategori === 'down_payment') {
                                    return [
                                        "Penerimaan dari Down Payment", 
                                        "Penerimaan dari Operasional Proyek",  // Kategori
                                        "Down Payment",  // Sub Kategori
                                        "KI0201M{$request->minggu_ke}{$tahun}"  // Code Account
                                    ];
                                }
                                break;
                            case 'dana_tunai_lainnya':
                                switch ($request->sub_kategori) {
                                    case 'kelebihan_tanah':
                                        return [
                                            "Biaya Kelebihan Tanah", 
                                            "Penerimaan dana Tunai lainnya",  // Kategori
                                            "Kelebihan Tanah",  // Sub Kategori
                                            "KI0301M{$request->minggu_ke}{$tahun}"  // Code Account
                                        ];
                                    case 'penambahan_spek':
                                        return [
                                            "Biaya Penambahan Spek bangunan", 
                                            "Penerimaan dana Tuni lainnya",  // Kategori
                                            "Penambahan Spek",  // Sub Kategori
                                            "KI0302M{$request->minggu_ke}{$tahun}"  // Code Account
                                        ];
                                    case 'selisih_kpr':
                                        return [
                                            "Biaya Selisih KPR", 
                                            "Penerimaan dana Tuni lainnya",  // Kategori
                                            "Selisih KPR",  // Sub Kategori
                                            "KI0303M{$request->minggu_ke}{$tahun}"  // Code Account
                                        ];
                                }
                                break;
                        }
                }
        }
    
        return [null, null, null, null];  // Return null if no match
    }
    
    
}


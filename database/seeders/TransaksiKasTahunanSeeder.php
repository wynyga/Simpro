<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransaksiKas;
use App\Models\CostTee;
use App\Models\Kwitansi;
use App\Models\LapBulanan;
use App\Helpers\KwitansiService;
use Carbon\Carbon;

class TransaksiKasTahunanSeeder extends Seeder
{
    public function run()
    {
        $costTees = CostTee::inRandomOrder()->limit(20)->get(); // Ambil 20 cost tee acak
        $tahunSekarang = now()->year;

        foreach ($costTees as $index => $costTee) {
            // Tentukan apakah ini kas masuk (101) atau keluar (102)
            $kode = $index % 2 === 0 ? '101' : '102';
            $jenis = $kode === '101' ? 'Penerimaan' : 'Pengeluaran';

            // Buat tanggal acak dalam tahun berbeda (2023, 2024, 2025) dan bulan berbeda
            $tahun = $tahunSekarang - rand(0, 2);
            $bulan = rand(1, 12);
            $tanggal = Carbon::create($tahun, $bulan, rand(1, 28));

            // Nominal acak antara 100rb - 5jt
            $jumlah = rand(100000, 5000000);

            // Simpan transaksi kas
            $kas = TransaksiKas::create([
                'tanggal' => $tanggal,
                'kode' => $kode,
                'jumlah' => $jumlah,
                'saldo_setelah_transaksi' => null,
                'metode_pembayaran' => 'Cash',
                'dibuat_oleh' => 'Seeder Tahunan',
                'keterangan_objek_transaksi' => "{$jenis} - {$costTee->description}",
                'perumahan_id' => $costTee->perumahan_id,
                'status' => 'approved',
                'sumber_transaksi' => 'cost_code',
                'keterangan_transaksi_id' => $costTee->id,
            ]);

            // Generate nomor kwitansi
            $prefix = $kode === '101' ? 'CI' : 'CO';
            $noDoc = KwitansiService::generateNoDoc($costTee->perumahan_id, $prefix);

            // Simpan kwitansi
            Kwitansi::create([
                'transaksi_kas_id' => $kas->id,
                'perumahan_id' => $kas->perumahan_id,
                'no_doc' => $noDoc,
                'tanggal' => $kas->tanggal,
                'dari' => $kas->dibuat_oleh,
                'jumlah' => $kas->jumlah,
                'untuk_pembayaran' => $kas->keterangan_objek_transaksi,
                'metode_pembayaran' => $kas->metode_pembayaran,
                'dibuat_oleh' => $kas->dibuat_oleh,
                'disetor_oleh' => $kas->dibuat_oleh,
                'mengetahui' => null,
                'gudang_in_id' => null,
            ]);

            LapBulanan::create([
                'perumahan_id' => $kas->perumahan_id,
                'cost_tee_id' => $costTee->id,
                'bulan' => (int) $kas->tanggal->format('m'),
                'tahun' => (int) $kas->tanggal->format('Y'),
                'jumlah' => $kas->jumlah,
                'code_account' => $kas->kode,
            ]);
        }
    }
}

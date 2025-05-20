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
        $faker = \Faker\Factory::create('id_ID');

        $costTees = CostTee::inRandomOrder()->limit(20)->get();

        // Target jumlah kas untuk masing-masing tahun
        $tahunTarget = [
            2025 => ['masuk' => 15000000, 'keluar' => 5000000],
            2024 => ['masuk' => 10000000, 'keluar' => 20000000],
            2023 => ['masuk' => 12000000, 'keluar' => 12000000],
        ];

        foreach ($tahunTarget as $tahun => $targets) {
            foreach (['masuk', 'keluar'] as $tipe) {
                $kode = $tipe === 'masuk' ? '101' : '102';
                $jenis = $tipe === 'masuk' ? 'Penerimaan' : 'Pengeluaran';
                $jenisTransaksi = $tipe === 'masuk' ? 'KASIN' : 'KASOUT';
                $total = $targets[$tipe];
                $sisa = $total;
                $i = 0;

                while ($sisa > 0) {
                    $costTee = $costTees[$i % $costTees->count()];
                    $i++;

                    $bulan = rand(1, 12);
                    $tanggal = Carbon::create($tahun, $bulan, rand(1, 28));
                    $jumlah = min(rand(500000, 3000000), $sisa);
                    $sisa -= $jumlah;

                    $kas = TransaksiKas::create([
                        'tanggal' => $tanggal,
                        'kode' => $kode,
                        'jumlah' => $jumlah,
                        'saldo_setelah_transaksi' => null,
                        'metode_pembayaran' => 'Cash',
                        'dibuat_oleh' => "Seeder Terkontrol",
                        'keterangan_objek_transaksi' => "{$jenis} - {$costTee->description}",
                        'perumahan_id' => $costTee->perumahan_id,
                        'status' => 'approved',
                        'sumber_transaksi' => 'cost_code',
                        'keterangan_transaksi_id' => $costTee->id,
                    ]);

                    $prefix = $kode === '101' ? 'CI' : 'CO';
                    $noDoc = KwitansiService::generateNoDoc($kas->perumahan_id, $prefix);

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
                        'bulan' => (int) $tanggal->format('m'),
                        'tahun' => (int) $tanggal->format('Y'),
                        'jumlah' => $kas->jumlah,
                        'code_account' => $kode,
                        'jenis_transaksi' => $jenisTransaksi,
                    ]);
                }
            }
        }
    }
}

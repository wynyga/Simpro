<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        $harga_standar = 174350000;
        $booking_fee = 2000000;

        for ($i = 1; $i <= 10; $i++) {
            // Random komponen tambahan harga
            $kelebihan_tanah = rand(0, 5000000);
            $penambahan_luas_bangunan = rand(0, 10000000);
            $perubahan_spek_bangunan = rand(0, 5000000);

            $total_harga_jual = $harga_standar + $kelebihan_tanah + $penambahan_luas_bangunan + $perubahan_spek_bangunan;

            // Transaksi 1–5 = LUNAS, 6–10 = HUTANG
            if ($i <= 5) {
                $minimum_dp = $total_harga_jual;
                $kpr_disetujui = 'Tidak';
            } else {
                $minimum_dp = (int)($total_harga_jual * 0.3);
                $kpr_disetujui = 'Ya';
            }

            Transaksi::create([
                'unit_id' => $i,
                'user_id' => $i,
                'harga_jual_standar' => $harga_standar,
                'kelebihan_tanah' => $kelebihan_tanah,
                'penambahan_luas_bangunan' => $penambahan_luas_bangunan,
                'perubahan_spek_bangunan' => $perubahan_spek_bangunan,
                'total_harga_jual' => $total_harga_jual,
                'minimum_dp' => $minimum_dp,
                'plafon_kpr' => $total_harga_jual - $minimum_dp,
                'kpr_disetujui' => $kpr_disetujui,
                'biaya_booking' => $booking_fee,
                'perumahan_id' => 1,
            ]);
        }
    }
}

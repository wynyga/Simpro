<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CostCentre;

class CostCenterSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['cost_centre_code' => 'KI010', 'description' => 'Penerimaan Booking Fee', 'cost_code' => 'KASIN'],
            ['cost_centre_code' => 'KI020', 'description' => 'Penerimaan dari Down Payment', 'cost_code' => 'KASIN'],
            ['cost_centre_code' => 'KI030', 'description' => 'Penerimaan Dana Tunai Lainnya', 'cost_code' => 'KASIN'],
            ['cost_centre_code' => 'KI040', 'description' => 'Penerimaan KPR', 'cost_code' => 'KASIN'],
            ['cost_centre_code' => 'KI050', 'description' => 'Share Capital Ordinary (Kantor Pusat / Modal Perseroan)', 'cost_code' => 'KASIN'],
            ['cost_centre_code' => 'KI060', 'description' => 'Penerimaan Kas Non Tunai Lainnya', 'cost_code' => 'KASIN'],
            ['cost_centre_code' => 'KI070', 'description' => 'Pembayaran / Pengembalian / Pelunasan Dana Piutang', 'cost_code' => 'KASIN'],
            ['cost_centre_code' => 'KI090', 'description' => 'Hutang Bank (Pencairan Dana Bank)', 'cost_code' => 'KASIN'],
            ['cost_centre_code' => 'KI100', 'description' => 'Hutang Pihak Ketiga (Pinjaman Pihak Ketiga)', 'cost_code' => 'KASIN'],
            ['cost_centre_code' => 'KI110', 'description' => 'Hutang Berelasi (Setoran dari Mitra Relasi atau Pemegang Saham)', 'cost_code' => 'KASIN'],

            ['cost_centre_code' => 'KO010', 'description' => 'Pembiayaan Project', 'cost_code' => 'KASOUT'],
            ['cost_centre_code' => 'KO020', 'description' => 'Pembiayaan Personalia, Administrasi dan Operasional Kantor', 'cost_code' => 'KASOUT'],
            ['cost_centre_code' => 'KO030', 'description' => 'Biaya Pinjaman / Pengembalian Pinjaman', 'cost_code' => 'KASOUT'],
            ['cost_centre_code' => 'KO040', 'description' => 'Dana Piutang (Dana Yg ada di Pihak Ketiga atau Ditahan Bank)', 'cost_code' => 'KASOUT'],
            ['cost_centre_code' => 'KO050', 'description' => 'Setor ke Kantor Pusat', 'cost_code' => 'KASOUT'],
        ];

        foreach ($data as $item) {
            CostCentre::create([
                'perumahan_id' => 1,
                'cost_centre_code' => $item['cost_centre_code'],
                'description' => $item['description'],
                'cost_code' => $item['cost_code'],
            ]);
        }
    }
}

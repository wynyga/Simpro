<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CostElement;
use App\Models\CostTee;

class CostTeeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['code' => 'KI010101', 'description' => 'Penerimaan Booking Fee'],
            ['code' => 'KI020101', 'description' => 'Penerimaan dari Down Payment'],
            ['code' => 'KI030101', 'description' => 'Biaya Kelebihan Tanah'],
            ['code' => 'KI030201', 'description' => 'Biaya Penambahan Spek bangunan'],
            ['code' => 'KI030301', 'description' => 'Biaya Selisih KPR'],
            ['code' => 'KI040101', 'description' => 'Penerimaan KPR'],
            ['code' => 'KI050101', 'description' => 'Share Capital Ordinary (Kantor Pusat / Modal Perseroan)'],
            ['code' => 'KI060101', 'description' => 'Penerimaan dari Penjulan Barang dan Jasa Kerja'],
            ['code' => 'KI060201', 'description' => 'Penerimaan dari Bunga Bank'],
            ['code' => 'KI070101', 'description' => 'Pencairan Piutang Retensi Bestek (IMB, Listrik, Air dan Jalan)'],
            ['code' => 'KI070102', 'description' => 'Pelunasan Piutang DP dan Selisih DP (Down Payment)'],
            ['code' => 'KO010501', 'description' => 'Biaya Alat Peraga Marketing'],
            ['code' => 'KO010502', 'description' => 'Insentif / Briging Fee / Fee Marketing'],
            ['code' => 'KO020101', 'description' => 'Biaya Gaji Karyawan'],
            ['code' => 'KO020201', 'description' => 'Biaya Telepon / Pulsa'],
            ['code' => 'KO020209', 'description' => 'Biaya Operasional Kantor Lain - lain'],
            ['code' => 'KO030401', 'description' => 'Pengembalian Pinjaman Pihak Berelasi'],
            ['code' => 'KO040107', 'description' => 'Piutang Pihak Ketiga'],
            ['code' => 'KO050101', 'description' => 'Setor ke Kantor Pusat'],
            // ... tambahkan sisanya sesuai list Anda
        ];

        foreach ($data as $item) {
            $teeCode = $item['code'];
            $elementCode = substr($teeCode, 0, 6); // ambil 6 digit pertama

            $costElement = CostElement::where('cost_element_code', $elementCode)->first();

            if (!$costElement) {
                echo "Cost Element $elementCode tidak ditemukan, skip...\n";
                continue;
            }

            CostTee::create([
                'perumahan_id' => $costElement->perumahan_id,
                'cost_element_code' => $elementCode,
                'cost_tee_code' => $teeCode,
                'description' => $item['description'],
            ]);
        }
    }
}

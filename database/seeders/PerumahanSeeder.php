<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PerumahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('perumahan')->insert([
            [
                'nama_perumahan' => 'Bumi Asih',
                'lokasi' => 'Jalan Cemara No. 12, Manado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perumahan' => 'Riverside',
                'lokasi' => 'Jalan Asri No. 15, Amurang',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

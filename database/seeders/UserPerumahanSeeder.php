<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserPerumahan;
use App\Models\Blok;

class UserPerumahanSeeder extends Seeder
{
    public function run()
    {
        $blokList = Blok::whereIn('nama_blok', ['A', 'B'])
                        ->where('perumahan_id', 1)
                        ->get();

        $noUrut = 1;

        foreach ($blokList as $blok) {
            for ($i = 1; $i <= 5; $i++) {
                $nomor = str_pad($i, 2, '0', STR_PAD_LEFT);
                UserPerumahan::create([
                    'nama_user' => "User {$blok->nama_blok}-$nomor",
                    'alamat_user' => "Perumahan Blok {$blok->nama_blok}-$nomor",
                    'no_telepon' => '0812' . str_pad((string)$noUrut++, 7, '0', STR_PAD_LEFT),
                    'perumahan_id' => 1,
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Perumahan;
use Illuminate\Support\Facades\Hash;

class AUserAndPerumahanSeeder extends Seeder
{
    public function run()
    {
        // Seed data perumahan
        $perumahanData = [
            [
                'nama_perumahan' => 'Griya Bumi Asih',
                'lokasi' => 'Jl. Permata No. 10, Jakarta',
                'inisial' => 'GBA'
            ],
            [
                'nama_perumahan' => 'Riverside Residence Tumpaan',
                'lokasi' => 'Jl. Permata No. 10, Jakarta',
                'inisial' => 'RRT'
            ]
        ];

        foreach ($perumahanData as $data) {
            Perumahan::updateOrCreate(
                ['nama_perumahan' => $data['nama_perumahan']],
                $data
            );
        }

        // Seed data user
        $userData = [
            [
                'name' => 'Wayan',
                'email' => 'Excavator@gmail.com',
                'role' => 'Admin',
                'password' => Hash::make('123456')
            ],
            [
                'name' => 'Wayan',
                'email' => 'Excavator2@gmail.com',
                'role' => 'Manager',
                'password' => Hash::make('123456')
            ],
                        [
                'name' => 'Wayan',
                'email' => 'Admin@gmail.com',
                'role' => 'Admin',
                'password' => Hash::make('123456')
            ],
            [
                'name' => 'Wayan',
                'email' => 'Manager@gmail.com',
                'role' => 'Manager',
                'password' => Hash::make('123456')
            ],
            [
                'name' => 'Komang',
                'email' => 'Direktur@gmail.com',
                'role' => 'Direktur',
                'password' => Hash::make('123456')
            ],
        ];

        foreach ($userData as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}

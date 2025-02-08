<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),  // Password di-hash menggunakan bcrypt
            'role' => 'admin',// Pastikan ID ini sesuai dengan yang ada di database anda
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AUserAndPerumahanSeeder::class,
            CostCenterSeeder::class,
            CostElementSeeder::class,
            CostTeeSeeder::class,
        ]);
        $this->call([
            DayWorkSeeder::class,
            TipeRumahSeeder::class,
            BlokSeeder::class,
            UnitSeeder::class,
            UserPerumahanSeeder::class,
            TransaksiSeeder::class
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserTableSeeder::class,
            ApartmentTableSeeder::class,
            ServiceTableSeeder::class,
            ApartmentServiceTableSeeder::class,
            MessageTableSeeder::class,
            SponsorTableSeeder::class,
            ApartmentSponsorTableSeeder::class,
            ViewTableSeeder::class,
            ImageTableSeeder::class,
        ]);
    }
}
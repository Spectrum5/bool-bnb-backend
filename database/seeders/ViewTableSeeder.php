<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Models
use App\Models\View;
use App\Models\Apartment;

// Helpers
use Faker\Generator as Faker;

class ViewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for ($i = 0; $i < 1000; $i++) {

        $newView = new View;

        $newView->apartment_id = Apartment::inRandomOrder()->first()->id;
        $newView->ip_address = $faker->ipv4();
        
        $newView->save();
    }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Models
use App\Models\Apartment;
use App\Models\Sponsor;

// Helpers
use Faker\Generator as Faker;

use function GuzzleHttp\Promise\all;

class ApartmentSponsorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        
        foreach (Apartment::all() as $apartment) {

            
            if ((rand(0, 100) > 90)) {
                
                $exp_date = $faker->dateTimeBetween('now', '+6 months');
                $sponsors = Sponsor::all()->take(rand(1, 3))->pluck('id');
    
                $apartment->sponsors()->attach($sponsors, ['exp_date'=> $exp_date]);
            }
        }
    }
}
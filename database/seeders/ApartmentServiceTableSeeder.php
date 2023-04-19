<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Model
use App\Models\Apartment;
use App\Models\Service;

use function GuzzleHttp\Promise\all;

class ApartmentServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Apartment::all() as $apartment) {
            $services = Service::all()->take(rand(1,13))->pluck('id');
        
            $apartment->services()->sync($services);
        }
    }
}
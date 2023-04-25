<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Model
use App\Models\Apartment;
use App\Models\User;

// Helpers
use Faker\Generator as Faker;
use Illuminate\Support\Str;

class ApartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {

        for ($i = 1; $i < 100; $i++) {

            $title = $faker->unique()->sentence(4);

            $newApartment = new Apartment;

            $newApartment->user_id = User::inRandomOrder()->first()->id;
            $newApartment->title = $title;
            $newApartment->slug = Str::slug($title);
            $newApartment->lat = $faker->latitude($min = -90, $max = 90);
            $newApartment->lng = $faker->longitude($min = -180, $max = 180);
            $newApartment->address = $faker->address();
            $newApartment->price = $faker->numberBetween(100, 1500);
            $newApartment->visibility = $faker->boolean();
            $newApartment->rooms_number = $faker->numberBetween(1, 8);
            $newApartment->bathrooms_number = $faker->numberBetween(1, 8);
            $newApartment->beds_number = $faker->numberBetween(1, 16);
            $newApartment->description = $faker->sentence(50);
            $newApartment->size = $faker->numberBetween(50, 500);

            $newApartment->save();
        }
    }
}
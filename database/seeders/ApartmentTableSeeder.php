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

        $hotels = config('hotels');

        foreach ($hotels as $singleApartment) {

            $newApartment = new Apartment;

            $title = strtolower($singleApartment['nome']);

            $newApartment->user_id = User::inRandomOrder()->first()->id;
            $newApartment->title = $title;
            $newApartment->slug = Str::slug($title);
            $newApartment->lat = $singleApartment['latitude'];
            $newApartment->lng = $singleApartment['longitude'];
            $newApartment->address = $singleApartment['address'];
            $newApartment->price = $faker->numberBetween(60, 1500);

            if ($newApartment->price >= 60 && $newApartment->price <= 200) {
                $newApartment->rooms_number = 1;
                $newApartment->bathrooms_number = 1;
                $newApartment->beds_number = random_int(1,2);
            }
            elseif ($newApartment->price > 200 && $newApartment->price <= 300) {
                $newApartment->rooms_number = 2;
                $newApartment->bathrooms_number = 2;
                $newApartment->beds_number = random_int(3, 4);
            }
            elseif ($newApartment->price > 300 && $newApartment->price <= 500) {
                $newApartment->rooms_number = 3;
                $newApartment->bathrooms_number = 3;
                $newApartment->beds_number = random_int(5, 6);
            }
            elseif ($newApartment->price > 500 && $newApartment->price <= 700) {
                $newApartment->rooms_number = 4;
                $newApartment->bathrooms_number = 4;
                $newApartment->beds_number = random_int(7, 8);
            }
            elseif ($newApartment->price > 700 && $newApartment->price <= 900) {
                $newApartment->rooms_number = 5;
                $newApartment->bathrooms_number = 5;
                $newApartment->beds_number = random_int(9, 10);
            }
            elseif ($newApartment->price > 900 && $newApartment->price <= 1100) {
                $newApartment->rooms_number = 6;
                $newApartment->bathrooms_number = 6;
                $newApartment->beds_number = random_int(11, 12);
            }
            elseif ($newApartment->price > 1100 && $newApartment->price <= 1300) {
                $newApartment->rooms_number = 7;
                $newApartment->bathrooms_number = 7;
                $newApartment->beds_number = random_int(13, 14);
            }
            elseif ($newApartment->price > 1300 && $newApartment->price <= 1500) {
                $newApartment->rooms_number = 8;
                $newApartment->bathrooms_number = 8;
                $newApartment->beds_number = random_int(15, 16);
            }

            $newApartment->description = $singleApartment['description'];
            $newApartment->size = $faker->numberBetween(50, 500);

            if (rand(1,100) > 90) {
                $newApartment->visibility = 0;
            }
            else $newApartment->visibility = 1;

            $newApartment->save();
        }
    }
}
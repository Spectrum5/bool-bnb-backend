<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Models
use App\Models\Apartment;
use App\Models\Image;

class ImageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $apartments = Apartment::all();
        // $baseUrl = [
        //     'placeholder-0.jpg', 'placeholder-1.jpg', 'placeholder-2.jpg', 'placeholder-3.jpg', 'placeholder-4.jpg', 
        //     'placeholder-5.jpg', 'placeholder-6.jpg', 'placeholder-7.jpg', 'placeholder-8.jpg', 'placeholder-9.jpg'
        // ];

        foreach ($apartments as $apartment) {

            $apartment = Apartment::find($apartment->id);

            for ($i=0; $i<=3; $i++) {
                $newImage = new Image;
                $newImage->url = 'placeholder-' . rand(1, 100) . '.jpg';
                $apartment->images()->save($newImage);
            }


            // $newImage = new Image;
            // $newImage->url = 'placeholder-' . rand(1, 100) . '.jpg';
            // $apartment->images()->save($newImage);
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Helpers
use Faker\Generator as Faker;
use Illuminate\Support\Str;

class MessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        
        for ($i = 0; $i < 10; $i++) {
            
            $name = $faker->firstName();

            $newMessage = new Message;

            $newMessage->apartment_id = Apartment::inRandomOrder()->first()->id;
            $newMessage->message = $faker->sentence(25);
            $newMessage->email = Str::lower($name) . '@email.com';;
            $newMessage->first_name = $name;
            $newMessage->last_name = $faker->lastName();

            $newMessage->save();
        }
    }
}
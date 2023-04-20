<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Helpers
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Model
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {

        // Utente per le prove
        $testUser = new User;
        $testUser->first_name = 'test f_name';
        $testUser->last_name = 'test l_name';
        $testUser->email = 'test@example.com';
        $testUser->password = Hash::make('passwordtest');
        $testUser->date_of_birth = '2000/01/01';
        $testUser->save();

        for ($i = 0; $i < 10; $i++) {
            $name = $faker->firstName();
            $surname = $faker->lastName();
            $password = $name . $surname . 'password';

            $newUser = new User;

            $newUser->first_name = Str::lower($name);
            $newUser->last_name = Str::lower($surname);
            $newUser->email = str_replace("'", '', Str::lower($name)) . '.' . str_replace("'", '', Str::lower($surname)) . '@email.com';
            $newUser->password = Hash::make($password);
            $newUser->date_of_birth = $faker->dateTimeBetween('-65 years', '-19 years');

            $newUser->save();
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Models
use App\Models\Sponsor;

class SponsorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sponsors = [
            [
                'title' => 'standard',
                'price' => 2.99,
                'duration' => 24
        
            ],
            [
                'title' => 'plus',
                'price' => 5.99,
                'duration' => 72
        
            ],
            [
                'title' => 'premium',
                'price' => 9.99,
                'duration' => 144
        
            ]
        ];

        foreach ($sponsors as $sponsor) {
            $newSponsor = Sponsor::create($sponsor);
        }
    }
}
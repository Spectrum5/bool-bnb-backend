<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Models
use App\Models\Service;

// Helpers
use Illuminate\Support\Str;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $services = [
            ['name' => 'wi-fi', 'icon' => 'wifi'],
            ['name' => 'tv', 'icon' => 'tv'],
            ['name' => 'aria condizionata', 'icon' => 'snowflake'],
            ['name' => 'lavatrice', 'icon' => 'soap'],
            ['name' => 'cassaforte', 'icon' => 'vault'],
            ['name' => 'piscina', 'icon' => 'water-ladder'],
            ['name' => 'parcheggio', 'icon' => 'car-side'],
            ['name' => 'sauna', 'icon' => 'hot-tub-person'],
            ['name' => 'cucina', 'icon' => 'utensils'],
            ['name' => 'colazione inclusa', 'icon' => 'mug-saucer'],
            ['name' => 'portineria', 'icon' => 'bell-concierge'],
            ['name' => 'servizio pulizie', 'icon' => 'broom'],
            ['name' => 'amico degli animali', 'icon' => 'paw'],
        ];

        foreach ($services as $service) {
            $newService = Service::create([
                'name' => $service['name'],
                'slug' => Str::slug($service['name']),
                'icon' => $service['icon']
            ]);
        }
    }
}
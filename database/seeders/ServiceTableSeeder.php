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
            ['name' => 'piscina', 'icon' => 'water-ladder'],
            ['name' => 'parcheggio', 'icon' => 'car-side'],
            ['name' => 'asciuga capelli', 'icon' => 'gun'],
            ['name' => 'cucina', 'icon' => 'kitchen-set'],
            ['name' => 'tv', 'icon' => 'tv'],
            ['name' => 'aria condizionata', 'icon' => 'snowflake'],
            ['name' => 'sauna', 'icon' => 'hot-tub-person'],
            ['name' => 'portineria', 'icon' => 'bell-concierge'],
            ['name' => 'servizio pulizie', 'icon' => 'broom'],
            ['name' => 'lavatrice', 'icon' => 'soap'],
            ['name' => 'amico degli animali', 'icon' => 'paw'],
            ['name' => 'cassaforte', 'icon' => 'vault'],
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
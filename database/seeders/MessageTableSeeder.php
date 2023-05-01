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

        $messages = [
            ['text' => 'Quali sono i servizi offerti dalla struttura, come la colazione inclusa o il servizio di pulizia giornaliero?'],
            ['text' => 'Quali sono le politiche sulla privacy e sulla sicurezza della struttura?'],
            ['text' => 'Quali sono le tipologie di camere disponibili e le loro caratteristiche?'],
            ['text' => 'Quali sono le opzioni di pagamento accettate?'],
            ['text' => 'Quali sono gli orari di check-in e check-out?'],
            ['text' => 'Quali sono le lingue parlate dal personale della struttura?'],
            ['text' => 'Quali sono i servizi aggiuntivi offerti, come la navetta per l\'aeroporto o il servizio in camera?'],
            ['text' => 'Quali sono le politiche di cancellazione e di rimborso?'],
            ['text' => 'Quali sono le attrazioni turistiche o i punti di interesse nelle vicinanze?'],
            ['text' => 'La struttura dispone di servizi per il deposito bagagli?'],
            ['text' => 'Quali sono le politiche di deposito cauzionale e di rimborso in caso di danni?'],
            ['text' => 'Quali sono le regole sulla pulizia dell\'appartamento e il servizio di pulizia?'],
            ['text' => 'L\'appartamento dispone di un balcone o di una terrazza?'],
            ['text' => 'Quali sono le regole sulla presenza di ospiti o visitatori?'],
            ['text' => 'La struttura dispone di una cassaforte o di un sistema di sicurezza per gli oggetti di valore degli ospiti?']
        ];
        
        for ($i=0; $i < 200; $i++) {

            $name = $faker->firstName();

            $newMessage = new Message;

            $newMessage->apartment_id = Apartment::inRandomOrder()->first()->id;
            $newMessage->message = $messages[rand(0,14)]['text'];
            $newMessage->email = Str::lower($name) . '@email.com';;

            $newMessage->save();
        }
    }
}
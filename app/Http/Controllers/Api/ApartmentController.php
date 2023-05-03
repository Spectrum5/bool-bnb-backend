<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Exception;

// Requests
use Illuminate\Http\Request;
use App\Http\Requests\Apartment\StoreApartmentRequest;
use App\Http\Requests\Apartment\UpdateApartmentRequest;

// Helpers
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\Apartment;
use App\Models\Service;
use App\Models\Sponsor;
use Spatie\FlareClient\Api;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Settings
        $apartmentsPerPage = 15;

        // Query
        $apartments = Apartment::where('visibility', 1)->with('images')->paginate($apartmentsPerPage);

        // Response
        if (isset($apartments) && count($apartments) > 0) {
            $response = [
                'success' => true,
                'message' => 'Appartamenti ottenuti con successo',
                'apartments' => $apartments
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Errore ottenimento Appartamenti'
            ];
        }

        return response()->json($response);
    }

    // Mostra una lista delle risorse relative solo all'id passato
    public function indexUser()
    {
        // Query
        $apartments = Apartment::where('user_id', Auth::user()->id)->get();

        // Response
        if (isset($apartments) && count($apartments) > 0) {
            $response = [
                'success' => true,
                'message' => 'Appartamenti personali ottenuti con successo',
                'apartments' => $apartments
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Errore ottenimento appartamenti personali"
            ];
        }

        return response()->json($response);
    }

    // Mostra una lista delle risorse filtrate secondo le query passate
    public function indexFilter(Request $request)
    {
        // Settings
        $apartmentsPerPage = 15;

        $distances = [];
        $query = Apartment::query();
        $apartments = new \Illuminate\Database\Eloquent\Collection;

        // Prende tutti gli apartments che hanno anche un record in apartment_sponsor
        $query
            ->leftJoin('apartment_sponsor', 'apartments.id', '=', 'apartment_sponsor.apartment_id')
            ->orderByRaw('CASE WHEN apartment_sponsor.exp_date > CURDATE() THEN 0 ELSE 1 END ASC')
            ->orderBy('apartment_sponsor.exp_date', 'ASC');

        // Filtro raggio
        if ($request->input('lat') != null && $request->input('lng') != null && $request->input('radius') != null) {
            $lat = $request->input('lat');
            $lng = $request->input('lng');
            $radius = $request->input('radius');

            $allApartments = Apartment::all();

            // Converte i gradi in radianti
            function deg2rad($deg)
            {
                return $deg * (pi() / 180);
            }

            // Resituisce la distanza tra due coppie di coordinate
            function getDistanceFromLatLonInKm($lat1, $lon1, $lat2, $lon2)
            {
                $earthRadiusKm = 6371; // Raggio della Terra in chilometri
                $dLat = deg2rad($lat2 - $lat1); // Differenza di latitudine in radianti
                $dLon = deg2rad($lon2 - $lon1); // Differenza di longitudine in radianti
                $a = sin($dLat / 2) * sin($dLat / 2) +
                    cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                    sin($dLon / 2) * sin($dLon / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadiusKm * $c; // Distanza in chilometri
                return $distance;
            }

            // Per ogni appartamento controlla se rientra nel raggio
            foreach ($allApartments as $apartment) {
                $distance = getDistanceFromLatLonInKm($lat, $lng, $apartment['lat'], $apartment['lng']);
                if ($distance <= $radius) {
                    $apartmentRadiusIds[] = $apartment->id;
                    $distances[] = $distance;
                }
            }

            array_multisort($distances, SORT_ASC, $apartmentRadiusIds);

            if (count($apartmentRadiusIds) > 0) {
                $query->whereIn('apartments.id', $apartmentRadiusIds);

                $apartmentRadiusIdsString = implode(',', $apartmentRadiusIds);

                $query->whereIn('apartments.id', $apartmentRadiusIds)->orderByRaw("FIELD(apartments.id, $apartmentRadiusIdsString)");
            }
        }

        // Filtro Rooms Number
        if ($request->input('rooms_number') != null) {
            $rooms_number = $request->input('rooms_number');
            $query->where('rooms_number', '>=', $rooms_number);
        }

        // Filtro Beds Number
        if ($request->input('beds_number') != null) {
            $beds_number = $request->input('beds_number');
            $query->where('beds_number', '>=', $beds_number);
        }

        // Filtro Bathrooms Number
        if ($request->input('bathrooms_number') != null) {
            $bathrooms_number = $request->input('bathrooms_number');
            $query->where('bathrooms_number', '>=', $bathrooms_number);
        }

        // Filtro Services
        if ($request->input('services') != null) {
            $services = $request->input('services');

            // Ottiene gli ID degli Apartments che hanno tutti i services in $services
            $apartmentServicesIds = DB::table('apartment_service')
                ->whereIn('service_id', $services)
                ->groupBy('apartment_id')
                ->havingRaw('COUNT(DISTINCT service_id) = ?', [count($services)])
                ->pluck('apartment_id')
                ->all();

            $query->whereIn('apartments.id', $apartmentServicesIds);
        }

        // $apartments = $query->with('images', 'sponsors')->select('apartments.*')->paginate($apartmentsPerPage);

        $apartments = $query->with(['sponsors' => function ($query) {
            $query->where('exp_date', '>', now())->first();
        }, 'images'])->select('apartments.*')->paginate($apartmentsPerPage);

        // Response
        if (isset($apartments) && count($apartments) > 0) {
            $response = [
                'success' => true,
                'message' => 'Appartamenti filtrati ottenuti con successo',
                'apartments' => $apartments,
                'Distanze ordinate' => $distances,
                'Ids ordinati' => $apartmentRadiusIds,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Nessun appartamento trovato"
            ];
        }

        return response()->json($response);
    }

    // Mostra una lista degli appartamenti con un piano di sponsor attivo
    public function indexSponsored(Request $request)
    {
        // Settings
        $apartmentsPerPage = 10;

        // Ottiene gli ID degli Apartments che hanno uno sponsor valido
        $apartmentSponsoredIds = DB::table('apartment_sponsor')
            ->whereDate('exp_date', '>=', now()->format('Y/m/d H:i'))
            ->groupBy('apartment_id')
            ->pluck('apartment_id')
            ->all();

        // Query
        $apartments = Apartment::whereIn('id', $apartmentSponsoredIds)->with('images', 'sponsors')->paginate($apartmentsPerPage);

        // Response
        if (isset($apartments) && count($apartments) > 0) {
            $response = [
                'success' => true,
                'message' => 'Appartamenti sponsorizzati ottenuti con successo',
                'apartments' => $apartments,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Nessun appartamento trovato"
            ];
        }

        return response()->json($response);
    }

    /**
     * Recupera le risorse per il form di creazione di una nuova risorsa
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::all();

        try {
            $response = [
                'success' => true,
                'message' => 'Servizi ottenuti con successo',
                'services' => $services
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => "Errore nell'ottenimento di servizi e sponsors"
            ];
        }

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreApartmentRequest $request)
    {
        // Validazione Dati
        $data = $request->validated();

        // Pulizia Titolo e Creazione Slug
        $title = strtolower($data['title']);
        $data['slug'] = Str::slug($title);

        if (Str::contains($title, ', ')) {
            $title = str_replace(',', ',' . ' ', $title);
        }

        try {
            // Creazione nuova istanza di Apartment e settaggio valori
            $newApartment = new Apartment();

            $newApartment->title = $title;
            $newApartment->slug = $data['slug'];
            $newApartment->lat = $data['lat'];
            $newApartment->lng = $data['lng'];
            $newApartment->address = $data['address'];
            $newApartment->price = $data['price'];
            $newApartment->beds_number = $data['beds_number'];
            $newApartment->rooms_number = $data['rooms_number'];
            $newApartment->bathrooms_number = $data['bathrooms_number'];
            $newApartment->visibility = $data['visibility'];
            $newApartment->size = $data['size'];
            $newApartment->description = $data['description'];
            $newApartment->user_id = $data['user_id'];

            $newApartment->save();

            // Collegamento dei servizi al nuovo appartamento
            $services = $data['services'];
            foreach ($services as $service) {
                $newApartment->services()->attach($service);
            };

            // Response
            $response = [
                'success' => true,
                'message' => 'Appartamento aggiunto con successo',
                'newApartment' => $newApartment,
                'apartment_id' => $newApartment->id
            ];
        } catch (Exception $e) {
            // Response
            $response = [
                'success' => false,
                'message' => 'Errore creazione nuovo Appartamento'
            ];
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        // Query
        $apartment = Apartment::where('slug', $slug)->with('services', 'user', 'sponsors', 'messages', 'images')->first();

        if ($apartment) {
            $response = [
                'success' => true,
                'message' => 'Appartamento Trovato',
                'apartment' => $apartment
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Appartamento non Trovato'
            ];
        }

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        // Query
        $apartment = Apartment::where('slug', $slug)->with('services', 'images')->first();

        // Response
        if ($apartment) {
            $response = [
                'success' => true,
                'message' => 'Appartamento da aggiornare ottenuto con successo',
                'apartment' => $apartment
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Errore nell'ottenimento dell'appartameno da aggiornare"
            ];
        }

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(UpdateApartmentRequest $request, $id)
    public function update(UpdateApartmentRequest $request, Apartment $apartment)
    {

        try {
            // Validazione
            $data = $request->validated();

            // Pulizia Titolo e Creazione Slug
            $title = strtolower($data['title']);
            // Ricalcoliamo lo slug  nel caso il titolo cambi
            $data['slug'] = Str::slug($title);

            // Query
            $apartment->update($data);

            // Response
            $response = [
                'success' => true,
                'message' => 'Appartamento aggiornato con successo',
                'apartment_id' => $apartment->id
            ];
        } catch (Exception $e) {
            // Response
            $response = [
                'success' => false,
                'message' => "Errore nell'aggiornamento dell'appartamento",
            ];
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Query
            Apartment::where('id', $id)->delete();

            // Response
            $response = [
                'success' => true,
                'message' => 'Appartamento eliminato con successo'
            ];
        } catch (Exception $e) {
            // Response
            $response = [
                'success' => false,
                'message' => 'Errore eliminazione appartamento'
            ];
        }

        return response()->json($response);
    }
}

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
use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Collection;
// \Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
// use GuzzleHttp\Client;

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
    public function index(Request $request)
    {
        $apartmentsPerPage = 15;

        $apartments = Apartment::with('images')->paginate($apartmentsPerPage);

        $response = [
            'success' => true,
            'message' => 'Appartamenti ottenuti con successo',
            'apartments' => $apartments
        ];

        return response()->json($response);
    }

    // Mostra una lista delle risorse relative solo all'id passato
    public function indexUser()
    {
        $apartments = Apartment::where('user_id', Auth::user()->id)->get();

        if (count($apartments) > 0) {
            $response = [
                'success' => true,
                'message' => 'Appartamenti personali ottenuti con successo',
                'apartments' => $apartments
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Errore nell'ottenimento degli appartamenti personali"
            ];
        }

        return response()->json($response);
    }

    // Mostra una lista delle risorse filtrate secondo le query passate
    public function indexFilter(Request $request)
    {
        $apartmentsPerPage = 15;

        $distances = [];
        $query = Apartment::query();
        $apartments = new \Illuminate\Database\Eloquent\Collection;

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
                $query->whereIn('id', $apartmentRadiusIds);

                $apartmentRadiusIdsString = implode(',', $apartmentRadiusIds);

                $query->whereIn('id', $apartmentRadiusIds)->orderByRaw("FIELD(id, $apartmentRadiusIdsString)");
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

            $query->whereIn('id', $apartmentServicesIds);
        }

        $apartments = $query->with('images', 'sponsors')->paginate($apartmentsPerPage);

        if (count($apartments) > 0) {
            $response = [
                'success' => true,
                'message' => 'Appartamenti filtrati ottenuti con successo',
                'apartments' => $apartments,
                'distanze ordinate' => $distances,
                'ids ordinati' => $apartmentRadiusIds
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Nessun appartamento filtrato trovato",

            ];
        }

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::all();
        $sponsors = Sponsor::all();

        try {
            $response = [
                'success' => true,
                'message' => 'Servizi e Sponsor ottenuti con successo',
                'services' => $services,
                'sponsors' => $sponsors,
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
        $data = $request->validated();

        $data['slug'] = Str::slug($data['title']);
        // $address = $data['address'];
        // $tomtom_api_key = 'Vru3uP06eapOxpYMujwrRlVLMB5Vkqch;';

        // $client = new Client();
        // $coordinates = $client->request('GET', 'https://api.tomtom.com/search/2/geocode/' . $address . '.json?key=' . $tomtom_api_key . '&typeahead=true&limit=1&radius=500');

        // if ($coordinates->getStatusCode() == 200) {
        //     // Ottieni il contenuto della risposta e decodificalo come JSON
        //     $response = $coordinates->getBody()->getContents();
        //     $responseDecode = json_decode($response);
        // }

        $title = strtolower($data['title']);

        if (Str::contains($title, ', ')) {
            $title = str_replace(',', ',' . ' ', $title);
        }

        $newApartment = new Apartment();

        $newApartment->title = $title;
        $newApartment->slug = Str::slug($data['title']);
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

        $services = $data['services'];
        foreach ($services as $service) {
            $newApartment->services()->attach($service);
        };

        $response = [
            'success' => true,
            'message' => 'Appartamento aggiunto con successo',
            'newApartment' => $newApartment,
            'allServices' => $services,
            'apartment_id' => $newApartment->id
        ];

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
        $apartment = Apartment::where('slug', $slug)->with('services', 'user', 'sponsors', 'messages', 'images')->first();

        if ($apartment) {
            $response = [
                'success' => true,
                'message' => 'success',
                'apartment' => $apartment
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'error'
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

        $apartment = Apartment::where('slug', $slug)->with('services', 'images')->first();
        $sponsors = Sponsor::all();

        if ($apartment && $sponsors) {
            $response = [
                'success' => true,
                'message' => 'Appartamento, Servizi e Sponsor ottenuti con successo',
                'apartment' => $apartment,
                'sponsors' => $sponsors,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Errore nell'ottenimento di appartameno, servizi e sponsors"
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

        $data = $request->validated();

        // Ricalcoliamo lo slug  nel caso il titolo cambi
        $data['slug'] = Str::slug($data['title']);

        $apartment->update($data);

        $response = [
            'success' => true,
            'message' => 'Apartment updated successfully.',
            'apartment_id' => $apartment->id
        ];

        return response()->json($response);

        // $apartment = Apartment::find($id)->with('services');

        // if (!$apartment) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Appartamento non trovato'
        //     ]);
        // }

        // $apartment->update($request->all());
        // return response()->json(["data" => [
        //     "success" => true,
        //     'message' => 'Appartamento aggiornato con successo',
        //     'apartment' => $apartment
        // ]]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Apartment::where('id', $id)->delete();

        $response = [
            'success' => true,
            'message' => 'Appartamento eliminato con successo'
        ];

        return response()->json($response);
    }
}

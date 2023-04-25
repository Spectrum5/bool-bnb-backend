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
        $apartmentsPerPage = 18;

        if ($request->input('user_id') != null) $user_id = $request->input('user_id');
        if ($request->input('address') != null) $address = $request->input('address');
        if ($request->input('rooms_number') != null) $rooms_number = $request->input('rooms_number');
        if ($request->input('beds_number') != null) $beds_number = $request->input('beds_number');
        if ($request->input('bathrooms_number') != null) $bathrooms_number = $request->input('bathrooms_number');
        if ($request->input('services') != null) $services = explode(',', $request->input('services'));

        // $services = explode(',', $request->input('services'));

        $query = Apartment::query();

        if (isset($address) || isset($rooms_number) || isset($beds_number) || isset($bathrooms_number) || isset($services)) {
            $apartments = new \Illuminate\Database\Eloquent\Collection;

            if ($services != null) {

                // Ottiene gli ID degli Apartments che hanno tutti i services in $services
                $apartmentIds = DB::table('apartment_service')
                    ->whereIn('service_id', $services)
                    ->groupBy('apartment_id')
                    ->havingRaw('COUNT(DISTINCT service_id) = ?', [count($services)])
                    ->pluck('apartment_id')
                    ->all();
            }

            if (isset($address)) {
                $query->where('address', 'LIKE', "%{$address}%");
            }
            if (isset($rooms_number)) {
                $query->where('rooms_number', '>=', $rooms_number);
            }
            if (isset($beds_number)) {
                $query->where('beds_number', '>=', $beds_number);
            }
            if (isset($bathrooms_number)) {
                $query->where('bathrooms_number', '>=', $bathrooms_number);
            }

            $apartments = $query->with('services')->get();
            $query->whereIn('id', $apartmentIds);
            $apartments = $query->with('services')->paginate($apartmentsPerPage);
        } else if (isset($user_id) && $user_id == Auth::user()->id) {
            $apartments = Apartment::where('user_id', $user_id)->with('services')->get();
        } else {
            $apartments = Apartment::with('services')->paginate($apartmentsPerPage);
        }

        // AGGIUNGERE VALIDAZIONI ID
        if ($apartments) {
            $response = [
                'success' => true,
                'message' => 'Appartamenti ottenuti con successo',
                'apartments' => $apartments
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Errore nell'ottenimento degli appartamenti"
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

        $newApartment = new Apartment();


        $newApartment->title = $data['title'];
        $newApartment->slug = Str::slug($data['title']);
        $newApartment->lat = $data['lat'];
        $newApartment->lng = $data['lng'];
        $newApartment->address = $data['address'];;
        $newApartment->price = $data['price'];
        $newApartment->image = $data['image'];
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
        $apartment = Apartment::where('slug', $slug)->with('services', 'user', 'sponsors', 'messages')->first();

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

        $apartment = Apartment::where('slug', $slug)->with('services')->first();
        $services = Service::all();
        $sponsors = Sponsor::all();

        if ($apartment && $services && $sponsors) {
            $response = [
                'success' => true,
                'message' => 'Appartamento, Servizi e Sponsor ottenuti con successo',
                'apartment' => $apartment,
                'services' => $services,
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
            'data' => $data
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

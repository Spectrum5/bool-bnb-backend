<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use Symfony\Component\Console\Input\Input;
use App\Http\Controllers\Api\Input;

use Exception;

// Requests
use Illuminate\Http\Request;
use App\Http\Requests\Apartment\StoreApartmentRequest;
use App\Http\Requests\Apartment\UpdateApartmentRequest;

// Helpers
use Illuminate\Support\Str;

// Models
use App\Models\Apartment;
use App\Models\Service;
use App\Models\Sponsor;

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
        $title = $request->input('title');
        $user_id = $request->input('user_id');

        if ($title) {
            $apartments = Apartment::where('title','LIKE',"%{$title}%")->with('services')->get();
        }


        else if ($user_id) {
            $apartments = Apartment::where('user_id', $user_id)->with('services')->get();
        }
        else {
            $apartments = Apartment::with('services')->paginate($apartmentsPerPage);
        }

        if ($apartments) {
            $response = [
                'success' => true,
                'message' => 'success',
                'apartments' => $apartments,
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

        $newApartment = new Apartment();

        $newApartment->title = $data['title'];
        $newApartment->slug = Str::slug($data['title']);
        $newApartment->lat = $data['lat'];
        $newApartment->lng = $data['lng'];
        $newApartment->address = $data['address'];
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
        $apartment = Apartment::where('slug', $slug)->with('services', 'user')->first();

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

<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

// Models
use App\Models\Sponsor;
use App\Models\Apartment;

// Helpers
use Illuminate\Http\Request;
use Carbon\Carbon;
// use Braintree\Gateway;

class SponsorController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sponsors = Sponsor::all();

        if ($sponsors) {
            $response = [
                'success' => true,
                'message' => 'Sponsor ottenuti con successo',
                'sponsors' => $sponsors,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Errore nell'ottenimento dei sponsor"
            ];
        }

        return response()->json($response);
    }

    // Crea un elemento di Apartment-Sponsor
    public function handlePayment(Request $request)
    {
        if ($request->input('sponsor_id')) $sponsor_id = $request->input('sponsor_id');
        if ($request->input('apartment_id')) $apartment_id = $request->input('apartment_id');

        // Creazione Elemento
        if(isset($sponsor_id) && $sponsor_id >= 1 && $sponsor_id <= count(Sponsor::all()) && isset($apartment_id)) {
            $apartment = Apartment::find($apartment_id);
    
            // 24, 72, 144 h
            $duration = Sponsor::find($request->input('sponsor_id'))->duration;

            // "2023-05-12T11:49:13.825217Z" data finale non formattata
            $date = Carbon::now()->addHours($duration);

            // 1, 3, 6 giorni
            $days = ceil($date->diffInHours(Carbon::now()) / 24);

            // $creation = '2023-05-02';
            $creation = Carbon::now()->format('Y/m/d H:i');

            $ending = \Carbon\Carbon::parse($creation)->addDays($days)->format('Y/m/d H:i');
    
            $apartment->sponsors()->attach($request->input('sponsor_id'), ['exp_date' => $ending]);

            // Response
            $response = [
                'success' => true,
                'message' => 'Appartamento Sponsorizzato con Successo',
                'apartment_id' => $apartment_id,
                'sponsor_id' => $apartment_id,
                'creation_date' => $creation,
                'ending_date' => $ending,
                'duration_hours' => $duration,
                'duration_days' => $days,
            ];
        }
        else {
            // Response
            $response = [
                'success' => false,
                'message' => 'Errore aggiunta Appartamento Sponsorizzato'
            ];
        }

        return response()->json($response);
    }

    /**
     * Get the token required to initialize the Braintree client
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function getClientToken()
    // {
    //     $gateway = new Gateway([
    //         'environment' => config('braintree.environment'),
    //         'merchantId' => config('braintree.merchant_id'),
    //         'publicKey' => config('braintree.public_key'),
    //         'privateKey' => config('braintree.private_key'),
    //     ]);

    //     $token = $gateway->ClientToken()->generate();

    //     return response()->json(['token' => $token]);
    // }

    /**
     * Process the payment and create the Sponsor record
     *
     * @param Request $request
     * @param int $apartment_id
     * @return \Illuminate\Http\JsonResponse
     */
    // public function processPayment(Request $request, $apartment_id)
    // {
    //     $gateway = new Gateway([
    //         'environment' => config('braintree.environment'),
    //         'merchantId' => config('braintree.merchant_id'),
    //         'publicKey' => config('braintree.public_key'),
    //         'privateKey' => config('braintree.private_key'),
    //     ]);

    //     $nonce = $request->payment_method_nonce;
    //     $amount = $request->amount;

    //     $result = $gateway->transaction()->sale([
    //         'amount' => $amount,
    //         'paymentMethodNonce' => $nonce,
    //         'options' => [
    //             'submitForSettlement' => true
    //         ]
    //     ]);

    //     if ($result->success) {
    //         $sponsor = new Sponsor();
    //         $sponsor->apartment_id = $apartment_id;
    //         $sponsor->start_date = now();
    //         $sponsor->end_date = now()->addHours($request->sponsor_duration);
    //         $sponsor->save();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Sponsor created successfully'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Payment failed'
    //         ]);
    //     }
    // }
}
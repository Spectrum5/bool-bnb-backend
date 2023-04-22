<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

// Requests
use Illuminate\Http\Request;
use App\Http\Requests\Message\StoreMessageRequest;

// Helpers
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Message;
use App\Models\Apartment;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // Ottiene l'id dello User Autenticato
        $logged_user_id = Auth::user()->id;
        // Ottiene tutti gli appartamenti dello user autenticato
        $logged_user_apartments = Apartment::where('user_id', $logged_user_id)->get();

        // Ottiene tutti gli id degli appartamenti
        foreach ($logged_user_apartments as $apartment) {
            $ids[] = $apartment->id;
        }

        // Ottiene i messaggi il cui apartment_id e' nella lista degli id degli appartamenti dell'utente autenticato
        $messages = Message::whereIn('apartment_id', $ids)->get();

        if ($messages) {
            $response = [
                'success' => true,
                'message' => 'Messaggi ottenuti con successo',
                'messages' => $messages,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Errore nell'ottenimento dei messaggi"
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
    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();

        $newMessage = new Message();

        $newMessage->email = $data['email'];
        $newMessage->message = $data['message'];
        $newMessage->apartment_id = $data['apartment_id'];

        $newMessage->save();

        $response = [
            'success' => true,
            'message' => 'Messaggio aggiunto con successo',
            'newMessage' => $newMessage
        ];

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $apartment = Apartment::where('slug', $slug)->with('services', 'user')->first();

        // if ($apartment) {
        //     $response = [
        //         'success' => true,
        //         'message' => 'success',
        //         'apartment' => $apartment
        //     ];
        // } else {
        //     $response = [
        //         'success' => false,
        //         'message' => 'error'
        //     ];
        // }

        // return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
}
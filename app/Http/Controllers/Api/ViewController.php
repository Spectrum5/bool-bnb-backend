<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Helpers
use Exception;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\View;

class ViewController extends Controller
{
    // Ritorna i records che hanno $id come apartment_id
    public function apartmentViews($id)
    {
        
        try {
            // Query
            $views = View::where('apartment_id', $id)->get();

            // Response
            $response = [
                'success' => true,
                'message' => 'Visualizzazioni ottenuto con successo',
                'count' => count($views)
            ];
        }
        catch (Exception $e) {
            // Response
            $response = [
                'success' => false,
                'message' => 'Errore ottenimento visualizzazioni'
            ];
        }
        
        return response()->json($response);
    }

    public function store(Request $request)
    {

        $apartment_id = $request->input('apartment_id');
        $ip_address = $request->input('ip_address');

        if (!View::where('apartment_id', $apartment_id)->where('ip_address', $ip_address)->exists()) {
            $newView = new View();

            $newView->apartment_id = $apartment_id;
            $newView->ip_address = $ip_address;

            $newView->save();

            $response = [
                'success' => true,
                'message' => 'View salvata con successo',
                'newView' => $newView,
            ];
        }
        else {
            $response = [
                'success' => false,
                'message' => 'Errore salvataggio View'
            ];
        }

        return response()->json($response);
    }
}
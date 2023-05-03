<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Query
        $services = Service::all();

        // Response
        if ($services) {
            $response = [
                'success' => true,
                'message' => 'Servizi ottenuti con successo',
                'services' => $services,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Errore nell'ottenimento dei servizi"
            ];
        }

        return response()->json($response);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Helpers
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        } catch (Exception $e) {
            // Response
            $response = [
                'success' => false,
                'message' => 'Errore ottenimento visualizzazioni'
            ];
        }

        return response()->json($response);
    }

    public function apartmentViewsMonths()
    {

        try {
            $user_id = Auth::user()->id;
            $year = 2023;

            // Query
            // V1
            // $viewsMonths = DB::table('views')
            //     ->join('apartments', 'views.apartment_id', '=', 'apartments.id')
            //     ->selectRaw('views.apartment_id, YEAR(views.created_at) as year, MONTH(views.created_at) as month, COUNT(*) as views_count')
            //     ->where('apartments.user_id', '=', $user_id)
            //     ->whereYear('views.created_at', '=', $year)
            //     ->groupBy('views.apartment_id', 'year', 'month')
            //     ->get();

            // V2
            // $viewsMonthsTemp = DB::table('views')
            //     ->join('apartments', 'views.apartment_id', '=', 'apartments.id')
            //     ->selectRaw('views.apartment_id, YEAR(views.created_at) as year, MONTH(views.created_at) as month, COUNT(*) as views_count')
            //     ->where('apartments.user_id', '=', $user_id)
            //     ->whereYear('views.created_at', '=', $year)
            //     ->groupBy('views.apartment_id', 'year', 'month')
            //     ->orderBy('views.apartment_id')
            //     ->get();

            // $currentApartmentId = 0;
            // $x = 0;

            // foreach($viewsMonthsTemp as $index => $view) {

            //     if($view->apartment_id != $currentApartmentId) {
            //         $viewsMonths[] = [
            //             'apartment_id' => $view->apartment_id,
            //             'year' => $year,
            //             'months' => [
            //                 [
            //                     'month' => $view->month,
            //                     'views' => $view->views_count
            //                 ]
            //             ]
            //         ];

            //         $x = $index;
            //         $currentApartmentId = $view->apartment_id;
            //     }
            //     else if ($view->apartment_id == $currentApartmentId) {
            //         $viewsMonths[$x]['months'][] = [
            //             'month' => $view->month,
            //             'views' => $view->views_count
            //         ];
            //     }

            // V3
            $viewsTemp = DB::table('views')
                ->join('apartments', 'views.apartment_id', '=', 'apartments.id')
                ->selectRaw('views.apartment_id, YEAR(views.created_at) as year, MONTH(views.created_at) as month, COUNT(*) as views_count')
                ->where('apartments.user_id', '=', $user_id)
                ->whereYear('views.created_at', '=', $year)
                // ->with(['apartment' => function ($query) {
                //     $query->select('title');
                // }])
                ->groupBy('views.apartment_id', 'year', 'month')
                ->orderBy('views.apartment_id')
                ->get();

            $viewsData = $viewsTemp->groupBy('apartment_id')->map(function ($apartmentViews, $apartment_id) {
                $months = $apartmentViews->map(function ($view) {
                    return ['month' => $view->month, 'views' => $view->views_count];
                });
                return ['apartment_id' => $apartment_id, 'months' => $months];
            })->values()->toArray();

            // Response
            $response = [
                'success' => true,
                'message' => 'Visualizzazioni mensili ottenuto con successo',
                'viewsData' => $viewsData
            ];
        } catch (Exception $e) {
            // Response
            $response = [
                'success' => false,
                'message' => 'Errore ottenimento visualizzazioni mensili'
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
        } else {
            $response = [
                'success' => false,
                'message' => 'Errore salvataggio View'
            ];
        }

        return response()->json($response);
    }
}

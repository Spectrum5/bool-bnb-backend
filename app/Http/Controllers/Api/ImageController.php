<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

// Requests
use Illuminate\Http\Request;
use App\Http\Requests\Image\StoreImageRequest;
use App\Http\Requests\Image\UpdateImageRequest;

// Models
use App\Models\Image;
use App\Models\Apartment;

// Helpers
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        try {
            $files = $request->allFiles();
            $id = $request['apartment_id'];

            foreach ($files as $index => $file) {
                $fileName = $file->getClientOriginalName();

                $fileName = Str::random(40) . $id . '.jpg';
                $path = 'apartments/' . $fileName;
                Storage::put($path, file_get_contents($file));

                $newImage = new Image;
                $newImage->url = $fileName;
                $apartment = Apartment::find($id);
                $apartment->images()->save($newImage);
            }

            $response = [
                'success' => true,
                'message' => 'Immagini aggiunte con successo',
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => "Errore nell'aggiunta delle immagini",
            ];
        }

        return response()->json($response);
    }

    public function show($id)
    {
        try {

            $images = Image::where('apartment_id', $id)->get();

            $response = [
                'success' => true,
                'message' => 'Immagini scaricate con successo',
                'images' => $images
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => "Errore nell'aggiunta delle immagini",
            ];
        }

        return response()->json($response);
    }

    public function update(Request $request)
    {
        try {
            $files = $request->allFiles();
            $id = $request['apartment_id'];

            foreach ($files as $index => $file) {
                $fileName = $file->getClientOriginalName();

                $fileName = Str::random(40) . $id . '.jpg';
                $path = 'apartments/' . $fileName;
                Storage::put($path, file_get_contents($file));

                $newImage = new Image;
                $newImage->url = $fileName;
                $apartment = Apartment::find($id);
                $apartment->images()->sync($newImage);
            }

            $response = [
                'success' => true,
                'message' => 'Immagini aggiornate con successo',
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => "Errore nell'aggiornamento delle immagini",
            ];
        }

        return response()->json($response);
    }
}
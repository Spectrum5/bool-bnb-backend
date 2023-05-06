<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers

// Models
use App\Http\Controllers\Api\ApartmentController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SponsorController;
use App\Http\Controllers\Api\ViewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Rotte Protette
Route::middleware(['auth:sanctum'])->get('/apartments/indexUser', [ApartmentController::class, 'indexUser']);
Route::middleware(['auth:sanctum'])->get('/apartments/create', [ApartmentController::class, 'create']);
Route::middleware(['auth:sanctum'])->post('/apartments', [ApartmentController::class, 'store']);
Route::middleware(['auth:sanctum'])->get('/apartments/{slug}/edit', [ApartmentController::class, 'edit']);
Route::middleware(['auth:sanctum'])->put('/apartments/{id}', [ApartmentController::class, 'update']);
Route::middleware(['auth:sanctum'])->delete('/apartments/{id}', [ApartmentController::class, 'destroy']);

// Rotte Pubbliche
Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/indexFilter', [ApartmentController::class, 'indexFilter']);
Route::get('/apartments/indexSponsored', [ApartmentController::class, 'indexSponsored']);
Route::get('/apartments/{slug}', [ApartmentController::class, 'show']);

// Versione funzionante
// Route::get('/apartments/indexSponsored', [ApartmentController::class, 'indexSponsored']);
// Route::get('/apartments/indexUser', [ApartmentController::class, 'indexUser']);
// Route::get('/apartments/indexFilter', [ApartmentController::class, 'indexFilter']);
// Route::resource('apartments', ApartmentController::class);

// Route::get('/apartments', [ApartmentController::class, 'index']);
// Route::get('/apartments/indexSponsored', [ApartmentController::class, 'indexSponsored']);
// Route::get('/apartments/indexFilter', [ApartmentController::class, 'indexFilter']);
// Route::get('/apartments/show/{id}', [ApartmentController::class, 'show']);
// Route::resource('apartments', ApartmentController::class)->middleware('auth:sanctum')->only(['indexUser', 'create', 'store', 'edit', 'update', 'destroy']);


Route::resource('messages', MessageController::class);
Route::resource('images', ImageController::class)->withoutMiddleware("throttle:api");
Route::resource('services', ServiceController::class);

// Rotta per la sponsorizzazione
Route::resource('sponsors', SponsorController::class);
Route::middleware(['auth:sanctum'])->post('/sponsors/handlePayment', [SponsorController::class, 'handlePayment']);

Route::post('/views', [ViewController::class, 'store']);
Route::get('/views/apartmentViews/{id}', [ViewController::class, 'apartmentViews']);
Route::middleware(['auth:sanctum'])->get('/views/apartmentViewsMonths', [ViewController::class, 'apartmentViewsMonths']);
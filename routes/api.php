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
    // Aggiungere rotte store, edit e destroy solo per utenti autenticati
});

// Rotte Protette
// Route::middleware(['auth:sanctum'])->resource('apartments', ApartmentController::class)->except(['index', 'show']);

// Rotte Pubbliche
// Route::get('/apartments', [ApartmentController::class, 'index']);

Route::get('/apartments/indexUser', [ApartmentController::class, 'indexUser']);
Route::get('/apartments/indexFilter', [ApartmentController::class, 'indexFilter']);
Route::get('/apartments/indexSponsored', [ApartmentController::class, 'indexSponsored']);
Route::get('/apartments/indexStats', [ApartmentController::class, 'indexStats']);

Route::resource('messages', MessageController::class);
Route::resource('images', ImageController::class)->withoutMiddleware("throttle:api");
Route::resource('services', ServiceController::class);
Route::resource('apartments', ApartmentController::class);

// Rotta per la sponsorizzazione
Route::resource('sponsors', SponsorController::class);
Route::post('/sponsors/handlePayment', [SponsorController::class, 'handlePayment']);

Route::post('/views', [ViewController::class, 'store']);
Route::get('/views/apartmentViews/{id}', [ViewController::class, 'apartmentViews']);
Route::get('/views/apartmentViewsMonths', [ViewController::class, 'apartmentViewsMonths']);
// Route::post('/sponsor', [SponsorController::class, 'store'])->name('sponsors.store');

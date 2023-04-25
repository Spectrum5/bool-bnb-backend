<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers

// Models
use App\Http\Controllers\Api\ApartmentController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ServiceController;

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
Route::resource('messages', MessageController::class);

Route::resource('apartments', ApartmentController::class);
Route::resource('images', ImageController::class);
Route::resource('services', ServiceController::class);
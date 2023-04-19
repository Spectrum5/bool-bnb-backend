<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers

// Models

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

Route::resource('apartments', ApartmentController::class);
Route::resource('images', ImageController::class);
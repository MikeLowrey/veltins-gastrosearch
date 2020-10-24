<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('start/', [App\Http\Controllers\GooglePlacesController::class, 'index']);
Route::get('getgeodatabyname/', [App\Http\Controllers\GooglePlacesController::class, 'get_geodata_by_name']);
Route::get('test/', [App\Http\Controllers\GooglePlacesController::class, 'test']);
Route::get('searchbyplace/', [App\Http\Controllers\GooglePlacesController::class, 'search_by_place']);




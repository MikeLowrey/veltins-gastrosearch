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

Route::get('getgeodatabyname/', [App\Http\Controllers\GooglePlacesController::class, 'get_geodata_by_name']);
Route::get('call/', [App\Http\Controllers\GooglePlacesController::class, 'call']);
Route::get('searchbyzip/{zip}', [App\Http\Controllers\DatabasePlacesItemsSearchController::class, 'search_by_zip']);
Route::get('searchbyzip/{zip}/{type}', [App\Http\Controllers\DatabasePlacesItemsSearchController::class, 'search_by_zip_and_type']);
Route::get('searchbyplace/', [App\Http\Controllers\GooglePlacesController::class, 'search_by_place']);

// Settings
Route::get('settings/', [App\Http\Controllers\SettingController::class, 'get_settings']);
Route::put('settings/file_format/{ext}', [App\Http\Controllers\SettingController::class, 'set_file_format']);
Route::put('settings/cache_duration/{days}', [App\Http\Controllers\SettingController::class, 'set_cache_duration'])->where('days', '[0-9]+');
Route::get('test/{test}', [App\Http\Controllers\GooglePlacesController::class, 'test']);


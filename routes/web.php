<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('download/{id}', [App\Http\Controllers\DownloadDataSheetAsExcelController::class, 'export']);
Route::get('/download/generate/{zip}/{type}', [App\Http\Controllers\DownloadDataSheetAsExcelController::class, 'export_by_zip_and_type']);

Route::get('download', [App\Http\Controllers\DownloadDataSheetAsExcelController::class, 'test']);

Route::get('statistics/', [App\Http\Controllers\PageController::class, 'test']);

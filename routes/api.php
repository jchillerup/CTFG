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

Route::post('/webhooks/listing/updated', [\App\Http\Controllers\Api\Webhooks\Airtable\ListingController::class, 'handleUpdate']);

Route::get('/tp/docu', [\App\Http\Controllers\Api\TP\DocuController::class, 'getConf']);
Route::post('/tp/docu/{name}', [\App\Http\Controllers\Api\TP\DocuController::class, 'updateConf']);
Route::post('/mail-category', [\App\Http\Controllers\Api\TP\DocuController::class, 'updateCat']);


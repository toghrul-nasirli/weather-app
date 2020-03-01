<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Zttp\Zttp;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/weather',function(){
    $apiKey = config('services.darksky.key');
    $latitude = request('latitude'); 
    $longitude = request('longitude'); 

    $response = Zttp::get("https://api.darksky.net/forecast/$apiKey/$latitude,$longitude?units=ca");

    return $response->json();
});

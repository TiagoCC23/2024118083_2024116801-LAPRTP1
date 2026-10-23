<?php

use App\Http\Controllers\Edificio;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('edificios', Edificio::class);

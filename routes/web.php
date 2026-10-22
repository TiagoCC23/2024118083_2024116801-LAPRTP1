<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AvaliacaoController;

Route::resource('edificios',EdificioController::class);
Route::resource('salas', SalaController::class);
Route::resource('reservas', ReservaController::class);
Route::resource('horarios', HorarioController::class);
Route::resource('avaliacoes', AvaliacaoController::class);

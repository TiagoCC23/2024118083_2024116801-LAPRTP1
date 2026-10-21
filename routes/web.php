<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EdificioController;

Route::resource('edificios',EdificioController::class);
Route::resource('salas', SalaController::class);
Route::resource('utilizadores', UtilizadoresController::class);
Route::resource('reservas', ReservaController::class);
Route::resource('horarios', HorariosController::class);
Route::resource('avaliacoes', AvaliacoesController::class);

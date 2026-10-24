<?php

use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SalaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth');
})->name('login');

Route::post('/login', function (Request $request) {
    return redirect()->route('home')->with('success', 'Sessão iniciada com sucesso!');
});

Route::post('/register', function (Request $request) {
    return redirect()->route('home')->with('success', 'Conta criada com sucesso!');
})->name('register');

Route::post('/logout', function (Request $request) {
    return redirect()->route('login');
})->name('logout');

Route::get('/home', function () {
    return view('home');
})->name('home');

/* Rotas principais */
Route::resource('reservas', ReservaController::class);
Route::resource('horarios', HorarioController::class);
Route::resource('edificios', EdificioController::class);
Route::resource('salas', SalaController::class);
Route::resource('avaliacoes', AvaliacaoController::class);

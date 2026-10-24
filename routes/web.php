<?php

use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SalaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Página de Autenticação (Login / Registo)
Route::get('/', function () {
    return view('auth');
})->name('login');

Route::post('/login', function (Request $request) {
    return redirect()->route('home')->with('success', 'Sessão iniciada com sucesso!');
});

Route::post('/register', function (Request $request) {
    return redirect()->route('home')->with('success', 'Conta criada com sucesso!');
})->name('register');

// Logout (Necessário para os formulários de logout dos headers)
Route::post('/logout', function (Request $request) {
    // Adiciona aqui Auth::logout() se estiveres a usar autenticação real
    return redirect()->route('login');
})->name('logout');

// Painel Principal (Home / Welcome)
Route::get('/home', function () {
    return view('home');
})->name('home');

/* Rotas extra do user */
Route::get('/salas/search', [SalaController::class, 'checkDisponibilidade'])->name('salas.pesquisar');
Route::get('/avaliacoes/self', [AvaliacaoController::class, 'minhasAvaliacoes'])->name('avaliacoes.minhas');
/* Rotas principais */
Route::resource('reservas', ReservaController::class);
Route::resource('horarios', HorarioController::class);
Route::resource('edificios', EdificioController::class);
Route::resource('salas', SalaController::class);
Route::resource('avaliacoes', AvaliacaoController::class);

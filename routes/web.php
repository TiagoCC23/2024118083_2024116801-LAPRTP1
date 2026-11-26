<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SalaController;
use Illuminate\Support\Facades\Route;


// 2. Rotas de Autenticação
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
            return view('auth.auth');
    });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);


    /* Google oAuth */
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware(['google.auth'])->group(function (){
    Route::get('/registo', [AuthController::class, 'showRegisto'])->name('register');
    Route::post('/registo', [AuthController::class, 'signin'])->name('auth.signin');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/conta/eliminar', [AuthController::class, 'deleteAccount'])->name('conta.delete');

// 3. Resources - Admin
Route::middleware(['admin'])->group(function () {
    Route::resource('edificios', EdificioController::class)->parameters(['edificios' => 'edificio'])->except(['index', 'show']);
    Route::resource('salas', SalaController::class)->parameters(['salas' => 'sala'])->except(['index', 'show']);
    Route::resource('horarios', HorarioController::class)->parameters(['horarios' => 'horario'])->except(['index', 'show']);
    Route::post('/reservas/importar.csv', [ReservaController::class, 'importarCsv'])->name('reservas.importar');
    });
});

// 3. Resources - Users

Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::match(['get', 'post'], '/salas/disponibilidade', [SalaController::class, 'checkDisponibilidade'])->name('salas.disponibilidade');
    Route::get('/minhas-reservas', [ReservaController::class, 'minhasReservas'])->name('reservas.minhas');
    Route::patch('/reservas/{reserva}/estado', [ReservaController::class, 'alterarEstado'])->name('reservas.alterarEstado');
    Route::get('/minhas-avaliacoes', [AvaliacaoController::class, 'minhasAvaliacoes'])->name('avaliacoes.minhas');

    Route::resource('reservas', ReservaController::class)->parameters(['reservas' => 'reserva']);
    Route::resource('avaliacoes', AvaliacaoController::class)->parameters(['avaliacoes' => 'avaliacao']);
    Route::resource('edificios', EdificioController::class)->parameters(['edificios' => 'edificio'])->only(['index', 'show']);
    Route::resource('salas', SalaController::class)->parameters(['salas' => 'sala'])->only(['index', 'show']);
    Route::resource('horarios', HorarioController::class)->parameters(['horarios' => 'horario'])->only(['index', 'show']);
});




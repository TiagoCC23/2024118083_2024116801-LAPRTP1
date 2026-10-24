<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SalaController;
use Illuminate\Support\Facades\Route;

// 1. Rota raiz que responde com status 200 (necessária para o ExampleTest)
Route::get('/', function () {
    return view('auth');
});

// 2. Rotas de Autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/registo', [AuthController::class, 'showRegisto'])->name('register');
Route::post('/registo', [AuthController::class, 'signin'])->name('auth.signin');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/conta/eliminar', [AuthController::class, 'deleteAccount'])->name('conta.delete');

// 3. Rotas personalizadas (devem vir antes dos resources com parâmetros curinga)
Route::post('/salas/disponibilidade', [SalaController::class, 'checkDisponibilidade'])->name('salas.disponibilidade');
Route::get('/minhas-reservas', [ReservaController::class, 'minhasReservas'])->name('reservas.minhas');
Route::patch('/reservas/{reserva}/estado', [ReservaController::class, 'alterarEstado'])->name('reservas.alterarEstado');
Route::get('/minhas-avaliacoes', [AvaliacaoController::class, 'minhasAvaliacoes'])->name('avaliacoes.minhas');

// 4. Resources
Route::resource('edificios', EdificioController::class)->parameters(['edificios' => 'edificio']);
Route::resource('salas', SalaController::class)->parameters(['salas' => 'sala']);
Route::resource('horarios', HorarioController::class)->parameters(['horarios' => 'horario']);
Route::resource('reservas', ReservaController::class)->parameters(['reservas' => 'reserva']);
Route::resource('avaliacoes', AvaliacaoController::class)->parameters(['avaliacoes' => 'avaliacao']);

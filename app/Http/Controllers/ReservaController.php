<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $caminhoCsv = storage_path('app/reservas.csv');
        $reservasCsv = [];

        if (file_exists($caminhoCsv)) {
            $linhas = array_map('str_getcsv', file($caminhoCsv));
            if (!empty($linhas)) {
                $cabecalho = array_shift($linhas);
                foreach ($linhas as $linha) {
                    if (count($cabecalho) === count($linha)) {
                        $reservasCsv[] = array_combine($cabecalho, $linha);
                    }
                }
            }
        }

        // Retorna a view reservas/show.blade.php (ou reservas/index)
        return view('reservas.index', compact('reservasCsv'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // 1. Captura a data enviada pela URL (ou usa a data de hoje como fallback)
        $dataSelecionada = $request->query('data', now()->toDateString());

        // 2. Busca os dados necessários para popular os selects do formulário
        $edificios = Edificio::all();
        $horarios = Horario::orderBy('hora_inicio')->get();

        // 3. Envia a data e os modelos para a view
        return view('reservas.create', compact('dataSelecionada', 'edificios', 'horarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserva $reserva)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        //
    }
}

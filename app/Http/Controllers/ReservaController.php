<?php

namespace App\Http\Controllers;
use App\Enums\TipoDeUso;
use App\Models\Reserva;
use App\Models\Sala;
use App\Enums\EstadoReserva;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\String_;

class ReservaController extends Controller
{

    private function rules(?Reserva $reserva = null): array
    {
        /* Procura a sala com base de dados com base no ID enviado no formulario*/
        $sala = Sala::find(request('sala_id'));

        /* Se a sala existir, usa a capacidade dela. Caso contrário, assume 0.*/
        $capacidadeMaxima = $sala ? $sala->capacidade_maxima : 0;

        return [
            'data'               => 'required|date',

            'quantidade_pessoas' => 'required|integer|min:1|max:' . $capacidadeMaxima,
            'estado'             => ['required', Rule::enum(EstadoReserva::class)],
            'criado_em'          => 'required|date_format:Y-m-d H:i:s',
            'tipo_de_uso'        => 'required|string|in:Pendente,Confirmada,Rejeitada,Cancelada',
            'sala_id'            => 'required|integer|exists:salas,id',
            'utilizador_id'      => ['required', 'integer', 'exists:utilizadores,id'],
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservas = Reserva::all();
        return view('reservas.index', compact('reservas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reservas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->rules());
        Reserva::create($validatedData);
        return redirect()->route('reservas.index')->with('success', 'Reserva criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        return view('reservas.show', ['reserva'=>$reserva]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        return view('reservas.edit', compact('reserva'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserva $reserva)
    {
            $validateData = $request->validate($this->rules($reserva));
            $reserva->update($validateData);
            return redirect()->route('reservas.index')->with('success', 'Atualizado com Sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        $reserva->delete();
        return redirect()->route('reservas.index')->with('success', 'Registo apagado com sucesso');
    }
}

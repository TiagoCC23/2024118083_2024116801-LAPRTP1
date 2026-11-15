<?php

namespace App\Http\Controllers;

use App\Enums\EstadoReserva;
use App\Models\Avaliacao;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AvaliacaoController extends Controller
{
    private function rules(?Avaliacao $avaliacao = null): array
    {
        return[
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:512',
            'reserva_id' => ['required', 'integer', 'exists:reservas,id', Rule::unique('avaliacoes', 'reserva_id')->ignore($avaliacao)],
            ];
    }
    public function minhasAvaliacoes()
    {
        $userId = Auth::id();

        $avaliacoes = Avaliacao::with(['reserva.sala.edificio', 'reserva.utilizador'])
            ->whereHas('reserva', fn ($query) => $query->where('utilizador_id', $userId))
            ->paginate(10);

        return view('avaliacoes.self', compact('avaliacoes'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $avaliacoes = Avaliacao::with(['reserva.sala.edificio', 'reserva.utilizador'])->paginate(10);
        return view('avaliacoes.index', compact('avaliacoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $avaliacao = new Avaliacao();
        $reservas = Reserva::with('sala.edificio')
            ->where('utilizador_id', Auth::id())
            ->where('estado', EstadoReserva::Confirmada)
            ->whereDoesntHave('avaliacao')
            ->orderByDesc('data')
            ->get();

        return view('avaliacoes.create', compact('avaliacao', 'reservas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate($this->rules());
        Avaliacao::create($validateData);
        return redirect()->route('avaliacoes.index')->with('success', 'Avaliação criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Avaliacao $avaliacao)
    {
        $avaliacao->load(['reserva.sala.edificio', 'reserva.utilizador']);
        return view('avaliacoes.show', compact('avaliacao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Avaliacao $avaliacao)
    {
        $avaliacao->load('reserva.sala.edificio');
        $reservas = Reserva::with('sala.edificio')
            ->where('id', $avaliacao->reserva_id)
            ->get();

        return view('avaliacoes.edit', compact('avaliacao', 'reservas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Avaliacao $avaliacao)
    {
        $validateData = $request->validate($this->rules($avaliacao));
        $avaliacao->update($validateData);
        return redirect()->route('avaliacoes.index')->with('success', 'Atualizado com Sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Avaliacao $avaliacao)
    {
        $avaliacao->delete();
        return redirect()->route('avaliacoes.index')->with('success', 'Registo apagado com sucesso');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
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

$avaliacoes = Avaliacao::whereHas('reserva', function ($query) use ($userId) {
    $query->where('utilizador_id', $userId);
})->get();
return view('avaliacoes.self', compact($avaliacoes));
}


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $avaliacao = Avaliacao::all();
        return view('avaliacoes.index', compact('avaliacao'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('avaliacoes.create');
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
        return view('avaliacoes.show', ['avaliacao'=>$avaliacao]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Avaliacao $avaliacao)
    {
        return view('avaliacoes.edit', compact('avaliacao'));
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

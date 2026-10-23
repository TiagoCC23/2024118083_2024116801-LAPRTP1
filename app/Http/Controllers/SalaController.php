<?php

namespace App\Http\Controllers;

use App\Enums\TipoSala;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalaController extends Controller
{

    private function rules(?Sala $sala = null): array
    {
        return[
            'nome_numero' => 'required|string',
            'capacidade_maxima' => 'required|integer|min:0',
            'tipo_sala' =>['required', Rule::enum(TipoSala::class)],
            'piso' => 'required|integer',
            'edificio_id' => 'required|integer|exists:edificios,id',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sala = Sala::all();
        return view('salas.index', compact('sala'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('salas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate($this->rules());
        return redirect()->route('salas.index')->with('success', 'SalaController criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sala $sala)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sala $sala)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sala $sala)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sala $sala)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\TipoSala;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalaController extends Controller
{

    /**
     * @param Sala|null $sala
     * @return array
     */
    private function rules(?Sala $sala = null): array
    {
        return[
            'nome_numero' => ['required', 'string', Rule::unique('salas')->ignore($sala)],
            'capacidade_maxima' => 'required|integer|min:0',

            'tipo_sala' =>'required|string|in:Exame,Trabalho,SalaDeAula,Outro',
            'piso' => 'required|integer',
            'edificio_id' => 'required|integer|exists:edificios,id',
        ];
    }


    public function checkDisponibilidade(Request $request)
    {
        $request->validate(['data' => 'required|date', 'capacidade_maxima' => 'required|integer|min:0',]);
        $dataRequest = $request->input('data');
        $capacidadeRequest = $request->input('capacidade_maxima');

        /* Muito AI gen, tenho de ver melhor ou tentar perceber esta querie*/
        $salasDisponiveis = Sala::where('capacidade_maxima', '>=', $capacidadeRequest)
            ->whereDoesntHave('reservas', function ($query) use ($dataRequest) {
                // Exclui as salas que tenham reservas para este dia e que não estejam canceladas/rejeitadas
                $query->whereDate('data', $dataRequest)
                    ->whereIn('estado', ['Pendente', 'Confirmada']);
            })
            ->get();
        return view('sala.disponibilidade', compact('salasDisponiveis', 'dataRequest', 'capacidadeRequest'));
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
        Sala::create($validateData);
        return redirect()->route('salas.index')->with('success', 'Sala criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sala $sala)
    {
        return view('salas.show', ['sala'=>$sala]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sala $sala)
    {
        return view('salas.edit', compact('sala'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sala $sala)
    {
        $validadeData = $request->validate($this->rules($sala));
        $sala->update($validadeData);
        return redirect()->route('salas.index')->with('success', 'Atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sala $sala)
    {
        $sala->delete();
        return redirect()->route('salas.index')->with('success', 'Registo apagado com sucesso');
    }
}

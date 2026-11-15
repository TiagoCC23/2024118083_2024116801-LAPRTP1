<?php

namespace App\Http\Controllers;

use App\Enums\Disponibilidade;
use App\Models\Edificio;
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
            'nome_numero' => ['required', 'string', 'max:50', Rule::unique('salas')->where(fn ($q) => $q->where('edificio_id', request('edificio_id')))->ignore($sala),],
            'capacidade_maxima' => 'required|integer|min:1',
            'tipo_sala' =>'required|string|in:Exame,Trabalho,SalaDeAula,Outro',
            'disponibilidade' => ['required',Rule::enum(Disponibilidade::class)],
            'piso' => 'required|integer',
            'edificio_id' => 'required|integer|exists:edificios,id',
        ];
    }


    public function checkDisponibilidade(Request $request)
    {
        $dataRequest = $request->input('data', date('Y-m-d'));
        $capacidadeRequest = $request->input('capacidade_maxima', 1);

        $request->merge([
            'data' => $dataRequest,
            'capacidade_maxima' => $capacidadeRequest,
        ])->validate([
            'data' => 'required|date',
            'capacidade_maxima' => 'required|integer|min:0',
        ]);

        $salasDisponiveis = Sala::with('edificio')
            ->where('capacidade_maxima', '>=', $capacidadeRequest)
            ->whereDoesntHave('reservas', function ($query) use ($dataRequest) {
                $query->whereDate('data', $dataRequest)
                    ->whereIn('estado', ['pendente', 'confirmada']);
            })
            ->get();

        return view('salas.disponibilidade', compact('salasDisponiveis', 'dataRequest', 'capacidadeRequest'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salas = Sala::with('edificio')->paginate(10);
        return view('salas.index', compact('salas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sala = new Sala();
        $edificios = Edificio::orderBy('nome')->get();
        $disponibilidades = Disponibilidade::cases();
        return view('salas.create', compact('sala', 'edificios', 'disponibilidades'));
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
        $sala->load('edificio');
        return view('salas.show', compact('sala'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sala $sala)
    {
        $edificios = Edificio::orderBy('nome')->get();
        $disponibilidades = Disponibilidade::cases();
        return view('salas.edit', compact('sala', 'edificios', 'disponibilidades'));
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

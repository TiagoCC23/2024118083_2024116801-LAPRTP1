<?php

namespace App\Http\Controllers;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HorarioController extends Controller
{
    /**
     * @param Horario|null $horario
     * @return array[]
     */
    private function rules(?Horario $horario = null): array
    {
        return [
            'hora_inicio' => ['required', 'date_format:H:i', Rule::unique('horarios')->ignore($horario)],
            'hora_fim'      => 'required|date_format:H:i|after:hora_inicio',
        ];
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $horarios = Horario::all();
        return view('horarios.index', compact('horarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('horarios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->rules());

        $validatedData['hora_inicio'] = strlen($validatedData['hora_inicio']) === 5
            ? $validatedData['hora_inicio'] . ':00'
            : $validatedData['hora_inicio'];

        $validatedData['hora_fim'] = strlen($validatedData['hora_fim']) === 5
            ? $validatedData['hora_fim'] . ':00'
            : $validatedData['hora_fim'];

        Horario::create($validatedData);
        return redirect()->route('horarios.index')->with('success', 'Horário criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Horario $horario)
    {
        return view('horarios.show', ['horario' => $horario]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Horario $horario)
    {
        return view('horarios.edit', compact('horario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Horario $horario)
    {
        $validatedData = $request->validate($this->rules($horario));

        $validatedData['hora_inicio'] = strlen($validatedData['hora_inicio']) === 5
            ? $validatedData['hora_inicio'] . ':00'
            : $validatedData['hora_inicio'];

        $validatedData['hora_fim'] = strlen($validatedData['hora_fim']) === 5
            ? $validatedData['hora_fim'] . ':00'
            : $validatedData['hora_fim'];

        $horario->update($validatedData);
        return redirect()->route('horarios.index')->with('success', 'Atualizado com Sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Horario $horario)
    {
        $horario->delete();
        return redirect()->route('horarios.index')->with('success', 'Registo apagado com sucesso');
    }
}

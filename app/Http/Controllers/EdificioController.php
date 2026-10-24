<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Edificio;
use Illuminate\Http\Request;

class EdificioController extends Controller
{
    /**
     * @param Edificio|null $edificio
     * @return array
     */
    private function rules(?Edificio $edificio = null): array
    {
        return [
            'nome'  => ['required', 'string', 'max:255', Rule::unique('edificios', 'nome')->ignore($edificio)],
            'sigla'  => ['required', 'string', 'max:5', Rule::unique('edificios', 'sigla')->ignore($edificio)],
            'imagem' => ['nullable', 'string'],
        ];
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('edificios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->rules());
        Edificio::create($validatedData);
        return redirect()->route('edificios.index')->with('success', 'Edifício criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Edificio $edificio)
    {
        return view('edificios.show', ['edificio' => $edificio]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Edificio $edificio)
    {
        return view('edificios.edit', compact('edificio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Edificio $edificio)
    {
        $validatedData = $request->validate($this->rules($edificio));
        $edificio -> update($validatedData);
        return redirect()->route('edificios.index')->with('success', 'Atualizado com Sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Edificio $edificio)
    {
        $edificio->delete();
        return redirect()->route('edificios.index')
            ->with('success', 'Registo apagado com sucesso');
    }
}

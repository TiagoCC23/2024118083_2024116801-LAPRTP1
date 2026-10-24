<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use Illuminate\Http\Request;

class EdificioController extends Controller
{
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
        $validatedData = $request->validate(['nome' => 'required|string|max:255',
                                             'sigla' => 'required|string|max:5',
                                             'imagem'=>'nullable|string']);
        Edificio::create($validatedData);
        return redirect()->route('edificios.index')->with('success', 'EdificioController criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $edificio = Edificio::findOrFail($id);
        return view('edificios.show', ['edificio' => $edificio]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $edificio = Edificio::findOrFail($id); // ALGO DE ERRADO NÃO ESTÁ CERTO
        return view('edificios.edit', compact('edificio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate(['nome' => 'required|string|max:255',
                                             'sigla' => 'required|string|max:5',
                                             'imagem'=>'nullable|string']);
        $edificio = Edificio::findOrFail($id);
        $edificio -> update($validatedData);
        return redirect()->route('edificios.index')->with('success', 'Atualizado com Sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $edificio = Edificio::findOrFail($id);
        $edificio->delete();
        return redirect()->route('edificios.index')
            ->with('success', 'Registo apagado com sucesso');
    }
}

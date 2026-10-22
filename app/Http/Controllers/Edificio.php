<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Edificio extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $edificios = \App\Models\Edificio::all();
        return view('edificios.index', compact('edificios'));
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
        $validatedData = $request->validate(['nome' => 'required |string | max:255', 'sigla' => 'required |string | max:5', 'imagem'=>'nullable|string']);
        $this->create($validatedData);
        return redirect()->route('edificios.index')->with('success', 'Edificio criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $edificios = \App\Models\Edificio::findOrFail($id); // ALGO DE ERRADO NÃO ESTÁ CERTO
        return view('edificios.edit', compact('edificios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

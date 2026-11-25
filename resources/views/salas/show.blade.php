@extends('layouts.app')

@section('title', 'UFP - ' . $sala->nome_numero)
@section('fundo', asset('images/fundo2.jpg'))

@section('conteudo')
    <div class="liquid-glass-card p-4 p-md-5 mx-auto" style="max-width: 750px;">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <span class="badge bg-white bg-opacity-10 text-white mb-2">{{ $sala->edificio->nome ?? '—' }}</span>
                <h2 class="font-display text-uppercase text-white fw-bold mb-0">{{ $sala->nome_numero }}</h2>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('salas.index') }}" class="btn btn-glass-secondary">Voltar</a>
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('salas.edit', $sala) }}" class="btn btn-minimal">Editar</a>
                @endif
            </div>
        </div>

        <div class="row g-3 text-white-50 small">
            <div class="col-md-6">Tipo de Sala: <strong class="text-white">{{ $sala->tipo_sala }}</strong></div>
            <div class="col-md-6">Capacidade Máxima: <strong class="text-white">{{ $sala->capacidade_maxima }} pessoas</strong></div>
            <div class="col-md-6">Piso: <strong class="text-white">{{ $sala->piso }}</strong></div>
            <div class="col-md-6">Disponibilidade: <x-badge-status :estado="$sala->disponibilidade" /></div>
        </div>
    </div>
@endsection

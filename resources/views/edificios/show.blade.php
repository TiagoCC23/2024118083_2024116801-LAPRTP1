@extends('layouts.app')

@section('title', 'UFP - ' . $edificio->nome)
@section('fundo', asset('images/fundo2.jpg'))

@section('conteudo')
    <div class="liquid-glass-card p-4 p-md-5 mx-auto" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <span class="badge bg-white bg-opacity-10 text-white mb-2">{{ $edificio->sigla }}</span>
                <h2 class="font-display text-uppercase text-white fw-bold mb-0">{{ $edificio->nome }}</h2>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('edificios.index') }}" class="btn btn-glass-secondary">Voltar</a>
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('edificios.edit', $edificio) }}" class="btn btn-minimal">Editar</a>
                @endif
            </div>
        </div>

        <p class="text-white-50 small mb-4">Imagem associada: <strong class="text-white">{{ $edificio->imagem ?? '—' }}</strong></p>

        <h5 class="text-uppercase text-white-50 small mb-3">Salas neste Edifício</h5>
        <ul class="list-group list-group-flush bg-transparent">
            @forelse($edificio->salas as $sala)
                <li class="list-group-item bg-transparent text-white border-white border-opacity-10 d-flex justify-content-between align-items-center px-0">
                    <a href="{{ route('salas.show', $sala) }}" class="text-white text-decoration-none fw-semibold">
                        {{ $sala->nome_numero }} (Piso {{ $sala->piso }})
                    </a>
                    <span class="text-white-50 small">Lotação: {{ $sala->capacidade_maxima }}</span>
                </li>
            @empty
                <li class="list-group-item bg-transparent text-white-50 px-0">Nenhuma sala associada.</li>
            @endforelse
        </ul>
    </div>
@endsection

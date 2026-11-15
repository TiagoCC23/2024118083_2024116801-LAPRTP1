@extends('layouts.app')

@section('title', 'UFP - Horário')
@section('fundo', asset('images/fundo3.jpg'))

@section('conteudo')
    <div class="liquid-glass-card p-4 p-md-5 mx-auto" style="max-width: 600px;">
        <h2 class="font-display text-uppercase text-white fw-bold mb-3">Detalhes do Horário #{{ $horario->id }}</h2>
        <p class="text-white-50 mb-1">Hora Início: <strong class="text-white">{{ substr($horario->hora_inicio, 0, 5) }}</strong></p>
        <p class="text-white-50 mb-4">Hora Fim: <strong class="text-white">{{ substr($horario->hora_fim, 0, 5) }}</strong></p>

        <div class="d-flex gap-2">
            <a href="{{ route('horarios.index') }}" class="btn btn-glass-secondary">Voltar</a>
            @if(auth()->user()?->isAdmin())
                <a href="{{ route('horarios.edit', $horario) }}" class="btn btn-minimal">Editar</a>
            @endif
        </div>
    </div>
@endsection

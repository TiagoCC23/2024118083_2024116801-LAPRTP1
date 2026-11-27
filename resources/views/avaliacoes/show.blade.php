@extends('layouts.app')

@section('title', 'UFP - Avaliação #' . $avaliacao->id)

@section('conteudo')
    <div class="liquid-glass-card p-4 p-md-5 mx-auto" style="max-width: 650px;">
        <h2 class="font-display text-uppercase text-white fw-bold mb-3">Detalhes da Avaliação #{{ $avaliacao->id }}</h2>

        <p class="text-white-50 mb-1">Sala: <strong class="text-white">{{ $avaliacao->reserva->sala->nome_numero ?? '—' }}</strong></p>
        <p class="text-white-50 mb-1">Utilizador: <strong class="text-white">{{ $avaliacao->reserva->utilizador->nome ?? '—' }}</strong></p>
        <p class="text-white-50 mb-1">Nota: <strong class="text-warning">{{ $avaliacao->nota }}/5</strong></p>
        <p class="text-white-50 mb-4">Comentário: <strong class="text-white">{{ $avaliacao->comentario ?? 'Sem comentário.' }}</strong></p>

        <div class="d-flex gap-2">
            <a href="{{ route('avaliacoes.index') }}" class="btn btn-glass-secondary">Voltar</a>
            @if($avaliacao->reserva?->utilizador_id === auth()->id())
                <a href="{{ route('avaliacoes.edit', $avaliacao) }}" class="btn btn-minimal">Editar</a>
            @endif
        </div>
    </div>
@endsection

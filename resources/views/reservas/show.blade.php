@extends('layouts.app')

@section('title', 'UFP - Detalhes da Reserva')
@section('fundo', asset('images/fundo4.webp'))

@section('conteudo')
    <div class="liquid-glass-card p-4 p-md-5 mx-auto" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <span class="text-uppercase text-white-50 small d-block mb-1">Reserva #{{ $reserva->id }}</span>
                <h2 class="font-display text-uppercase text-white fw-bold mb-0">
                    {{ $reserva->sala->nome_numero ?? '—' }} · {{ $reserva->data?->format('d/m/Y') }}
                </h2>
            </div>
            <x-badge-status :estado="$reserva->estado" />
        </div>

        <div class="row g-3 text-white-50 small mb-4">
            <div class="col-md-6">Edifício: <strong class="text-white">{{ $reserva->sala->edificio->nome ?? '—' }}</strong></div>
            <div class="col-md-6">Utilizador: <strong class="text-white">{{ $reserva->utilizador->nome ?? '—' }}</strong></div>
            <div class="col-md-6">Tipo de Uso: <strong class="text-white">{{ $reserva->tipo_de_uso_label }}</strong></div>
            <div class="col-md-6">Pessoas: <strong class="text-white">{{ $reserva->quantidade_pessoas }}</strong></div>
            <div class="col-12">
                Horários:
                @foreach($reserva->horarios as $h)
                    <span class="badge bg-white bg-opacity-10 text-white me-1">{{ $h->intervalo }}</span>
                @endforeach
            </div>
        </div>

        @if($reserva->avaliacao)
            <div class="p-3 rounded-3 bg-white bg-opacity-10 mb-4">
                <strong class="text-white d-block mb-1">Avaliação ({{ $reserva->avaliacao->nota }}/5)</strong>
                <p class="text-white-50 small mb-0">{{ $reserva->avaliacao->comentario ?? 'Sem comentário.' }}</p>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between gap-2 pt-3 border-top border-white border-opacity-10">
            <a href="{{ route('reservas.index') }}" class="btn btn-glass-secondary">Voltar</a>

            <div class="d-flex gap-2">
                @can('avaliar', $reserva)
                    <a href="{{ route('avaliacoes.create') }}" class="btn btn-minimal">Avaliar</a>
                @endcan

                @can('update', $reserva)
                    <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-glass-secondary">Editar</a>
                @endcan

                @can('confirmar', $reserva)
                    <form action="{{ route('reservas.alterarEstado', $reserva) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="estado" value="confirmada">
                        <button type="submit" class="btn btn-minimal">Confirmar</button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endsection

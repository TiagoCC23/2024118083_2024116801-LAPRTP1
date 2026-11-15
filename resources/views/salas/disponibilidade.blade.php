@extends('layouts.app')

@section('title', 'UFP - Disponibilidade Salas')
@section('fundo', asset('images/fundo3.jpg'))

@push('styles')
    <style>
        .btn-liquid-sm {
            background-color: #ffffff;
            color: #000000;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.7rem;
            padding: 0.4rem 1rem;
            border-radius: 50rem;
            text-decoration: none;
        }
        .btn-liquid-sm:hover { background-color: #e2e8f0; color: #000000; }
    </style>
@endpush

@section('conteudo')
    <div class="mx-auto" style="max-width: 1000px;">
        @php
            $carbonData = \Carbon\Carbon::parse($dataRequest ?? date('Y-m-d'))->locale('pt');
        @endphp

        <div class="liquid-glass-card p-4 mb-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1 text-uppercase mb-2" style="font-size: 0.65rem;">
                    Universidade Fernando Pessoa
                </span>
                    <h3 class="font-display text-uppercase text-white fw-bold mb-1">Salas Disponíveis</h3>
                    <p class="text-white-50 small mb-0">
                        Capacidade mínima solicitada: <strong class="text-white">{{ $capacidadeRequest ?? 1 }}</strong> pessoas.
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="text-uppercase text-white-50 small d-block" style="font-size: 0.65rem;">Data Selecionada</span>
                    <span class="fw-bold text-white fs-5">{{ $carbonData->format('d/m/Y') }}</span>
                    <span class="text-white-50 small d-block">({{ ucfirst($carbonData->translatedFormat('l')) }})</span>
                </div>
            </div>
        </div>

        <div class="glass-schedule-card p-3 p-md-4">
            <div class="table-responsive">
                <table class="table table-cronograma align-middle">
                    <thead>
                    <tr>
                        <th>Sala</th>
                        <th>Edifício</th>
                        <th>Tipo</th>
                        <th>Capacidade</th>
                        <th>Estado</th>
                        <th class="text-end">Ação</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($salasDisponiveis as $sala)
                        <tr>
                            <td class="fw-bold text-white">{{ $sala->nome_numero }}</td>
                            <td class="text-white-50">{{ $sala->edificio->nome ?? '—' }}</td>
                            <td class="text-white-50">{{ $sala->tipo_sala }}</td>
                            <td class="text-white-50">{{ $sala->capacidade_maxima }}</td>
                            <td><x-badge-status :estado="$sala->disponibilidade" /></td>
                            <td class="text-end">
                                <a href="{{ route('reservas.create', ['data' => $dataRequest]) }}" class="btn-liquid-sm">
                                    Reservar <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-white-50">
                                Nenhuma sala disponível para os critérios selecionados.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

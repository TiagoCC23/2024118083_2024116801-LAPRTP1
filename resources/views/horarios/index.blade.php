<!-- resources/views/horarios/index.blade.php -->
@extends('layouts.app')

@section('title', 'UFP - Grade de Horários')

@section('content')

    <!-- PAINEL SUPERIOR -->
    <div class="liquid-glass-card p-4 p-md-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <span class="badge bg-white bg-opacity-10 text-white border border-white-20 rounded-pill px-3 py-1 text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.15em;">
                    Universidade Fernando Pessoa
                </span>
                <h3 class="font-display text-uppercase text-white fw-bold mb-1">
                    Grade Horária de Disponibilidade
                </h3>
                <p class="text-white-50 small mb-0">
                    Consulte os slots de 1 hora para planeamento das salas de estudo.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                @php
                    $dataString = request('data', date('Y-m-d'));
                    $carbonData = \Carbon\Carbon::parse($dataString)->locale('pt');
                @endphp
                <div class="d-inline-block text-start text-md-end border-start border-md-0 ps-3 ps-md-0">
                    <span class="text-uppercase text-white-50 small d-block" style="font-size: 0.65rem; letter-spacing: 0.1em;">Data Selecionada</span>
                    <span class="fw-bold text-white fs-5">{{ $carbonData->format('d/m/Y') }}</span>
                    <span class="text-white-50 small d-block">({{ ucfirst($carbonData->translatedFormat('l')) }})</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TABELA DE HORÁRIOS -->
    <div class="liquid-glass-card p-3 p-md-4">
        <div class="table-responsive">
            <table class="table table-horarios align-middle">
                <thead>
                <tr>
                    <th>Intervalo de Horário</th>
                    <th>Duração</th>
                    <th>Salas Livres</th>
                    <th>Estado Geral</th>
                    <th class="text-end">Ação</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $blocosHorarios = $horarios ?? [
                        ['inicio' => '08:00', 'fim' => '09:00', 'livres' => 12],
                        ['inicio' => '09:00', 'fim' => '10:00', 'livres' => 8],
                        ['inicio' => '10:00', 'fim' => '11:00', 'livres' => 3],
                        ['inicio' => '11:00', 'fim' => '12:00', 'livres' => 0],
                        ['inicio' => '12:00', 'fim' => '13:00', 'livres' => 15],
                    ];
                @endphp

                @foreach($blocosHorarios as $item)
                    @php
                        $inicio = is_array($item) ? $item['inicio'] : substr($item->hora_inicio, 0, 5);
                        $fim = is_array($item) ? $item['fim'] : substr($item->hora_fim, 0, 5);
                        $livres = is_array($item) ? $item['livres'] : ($item->salas_livres ?? rand(1, 10));
                        $isOcupado = $livres === 0;
                    @endphp
                    <tr>
                        <td class="fw-bold text-white fs-6">
                            <i class="bi bi-clock me-2 text-white-50"></i>
                            {{ $inicio }} — {{ $fim }}
                        </td>
                        <td class="text-white-50">1 Hora</td>
                        <td>
                                <span class="fw-semibold {{ $isOcupado ? 'text-white-50' : 'text-white' }}">
                                    {{ $livres }} {{ $livres == 1 ? 'sala disponível' : 'salas disponíveis' }}
                                </span>
                        </td>
                        <td>
                            @if(!$isOcupado)
                                <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-30 rounded-pill px-3 py-1 text-uppercase" style="font-size: 0.65rem;">Disponível</span>
                            @else
                                <span class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-30 rounded-pill px-3 py-1 text-uppercase" style="font-size: 0.65rem;">Lotação Esgotada</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if(!$isOcupado)
                                <a href="{{ route('reservas.create', ['data' => $dataString, 'horario' => $inicio]) }}" class="btn btn-light btn-sm rounded-pill fw-bold text-uppercase px-3" style="font-size: 0.7rem;">
                                    Reservar <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            @else
                                <span class="text-white-50 small pe-2">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

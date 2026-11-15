@extends('layouts.app')

@section('title', 'UFP - Cronograma de Reservas')

@section('conteudo')
    <div class="mx-auto" style="max-width: 1100px;">
        <div class="text-center mb-4">
            <h1 class="font-display display-4 fw-bold text-uppercase text-white lh-1 mb-3">
                Cronograma de Reservas
            </h1>

            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('reservas.create') }}" class="btn btn-minimal">
                    <i class="bi bi-plus-lg me-1"></i> Nova Reserva
                </a>
            </div>
        </div>

        <div class="glass-schedule-card p-2 p-md-3">
            <div class="table-responsive">
                <table class="table table-cronograma align-middle">
                    <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Edifício</th>
                        <th>Sala</th>
                        <th>Tipo de Uso</th>
                        <th>Nº Pessoas</th>
                        <th class="text-center">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($reservasCsv ?? $reservas ?? [] as $linha)
                        <tr>
                            <td class="fw-semibold text-white">
                                <i class="bi bi-calendar3 me-2 text-white-50"></i>
                                {{ $linha['data'] ?? $linha->data ?? '—' }}
                            </td>
                            <td>
                                <i class="bi bi-clock me-1 text-white-50"></i>
                                {{ $linha['horario'] ?? $linha->horario ?? '—' }}
                            </td>
                            <td class="text-white-50">
                                {{ $linha['edificio'] ?? $linha->edificio->nome ?? '—' }}
                            </td>
                            <td class="fw-bold text-white">
                                {{ $linha['sala'] ?? $linha->sala->nome_numero ?? '—' }}
                            </td>
                            <td class="text-white-50">
                                {{ $linha['tipo_de_uso'] ?? $linha->tipo_de_uso ?? 'Estudo' }}
                            </td>
                            <td class="text-white-50">
                                <i class="bi bi-people me-1"></i> {{ $linha['quantidade_pessoas'] ?? $linha->quantidade_pessoas ?? 1 }}
                            </td>
                            <td class="text-center">
                                @php
                                    $estado = strtolower($linha['estado'] ?? $linha->estado ?? 'pendente');
                                    $badgeClass = match($estado) {
                                        'confirmada' => 'badge-confirmada',
                                        'cancelada', 'rejeitada' => 'badge-cancelada',
                                        default => 'badge-pendente',
                                    };
                                @endphp
                                <span class="badge-status {{ $badgeClass }}">
                                    {{ ucfirst($estado) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-white-50">
                                Não existem reservas registadas de momento.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

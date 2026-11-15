@extends('layouts.app')

@section('title', 'UFP - Horário Semanal')
@section('fundo', asset('images/fundo3.jpg'))

@push('styles')
    <style>
        .table-weekly { border-collapse: separate; border-spacing: 0; }
        .table-weekly th.header-day {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        .table-weekly td.header-hour {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            width: 140px;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        .table-weekly td.slot-cell {
            background: rgba(255, 255, 255, 0.02) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            height: 58px;
            vertical-align: middle;
            text-align: center;
            padding: 0.4rem !important;
        }
        .slot-item {
            display: block;
            padding: 0.3rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
        }
        .slot-free {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }
    </style>
@endpush

@section('conteudo')
    <div class="liquid-glass-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="font-display text-uppercase text-white fw-bold mb-1">Horário Semanal</h2>
                <p class="text-white-50 small mb-0">Blocos horários disponíveis para reserva.</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('horarios.create') }}" class="btn btn-minimal">
                        <i class="bi bi-plus-lg me-1"></i> Novo Horário
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="liquid-glass-card p-3 p-md-4 mb-4">
        <div class="table-responsive">
            <table class="table table-weekly align-middle">
                <thead>
                <tr>
                    <th class="header-day">Bloco</th>
                    @foreach(['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'] as $dia)
                        <th class="header-day">{{ $dia }}</th>
                    @endforeach
                    @if(auth()->user()?->isAdmin())
                        <th class="header-day">Ações</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @forelse($horarios as $horario)
                    <tr>
                        <td class="header-hour">{{ $horario->intervalo }}</td>
                        @foreach(['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'] as $dia)
                            <td class="slot-cell">
                                <a href="{{ route('reservas.create') }}" class="slot-item slot-free">
                                    <i class="bi bi-plus-circle me-1"></i> Reservar
                                </a>
                            </td>
                        @endforeach
                        @if(auth()->user()?->isAdmin())
                            <td class="slot-cell">
                                <a href="{{ route('horarios.show', $horario) }}" class="btn btn-sm btn-link text-white-50 p-0 me-2"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('horarios.edit', $horario) }}" class="btn btn-sm btn-link text-white-50 p-0 me-2"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('horarios.destroy', $horario) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar este horário?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-white-50">Nenhum horário registado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

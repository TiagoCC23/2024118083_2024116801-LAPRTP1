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
            width: 100%;
            padding: 0.35rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .slot-free {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }
        .slot-free:hover:not(:disabled) {
            background: rgba(16, 185, 129, 0.3);
            color: #ffffff;
        }
        .slot-selected {
            background: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #ffffff !important;
            font-weight: 800;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.35);
        }
        .slot-disabled {
            background: rgba(255, 255, 255, 0.03) !important;
            color: rgba(255, 255, 255, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
            cursor: not-allowed !important;
        }
    </style>
@endpush

@section('conteudo')
    @php
        $dias = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
    @endphp

    <div class="liquid-glass-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="font-display text-uppercase text-white fw-bold mb-1">Horário Semanal</h2>
                <p class="text-white-50 small mb-0">Blocos horários disponíveis para reserva (máx. 3 blocos seguidos).</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <form id="form-prosseguir-reserva" action="{{ route('reservas.create') }}" method="GET" class="d-flex gap-2 align-items-center m-0">
                    <div id="horarios-hidden-inputs"></div>

                    <button type="button" id="btn-limpar-selecao" class="btn btn-glass-secondary d-none">
                        Limpar
                    </button>
                    <button type="submit" id="btn-prosseguir-reserva" class="btn btn-liquid py-2 px-4 d-none">
                        Prosseguir Reserva (<span id="count-blocos">0</span>) <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>

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
                    @foreach($dias as $dia)
                        <th class="header-day">{{ $dia }}</th>
                    @endforeach
                    @if(auth()->user()?->isAdmin())
                        <th class="header-day">Ações</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @forelse($horarios as $rowIndex => $horario)
                    <tr>
                        <td class="header-hour">{{ $horario->intervalo }}</td>
                        @foreach($dias as $colIndex => $dia)
                            <td class="slot-cell">
                                <button type="button"
                                        class="slot-item slot-free btn-slot-horario"
                                        data-col="{{ $colIndex }}"
                                        data-row="{{ $rowIndex }}"
                                        data-horario-id="{{ $horario->id }}">
                                    <i class="bi bi-plus-circle me-1"></i> Reservar
                                </button>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const botoes = Array.from(document.querySelectorAll('.btn-slot-horario'));
            const btnProsseguir = document.getElementById('btn-prosseguir-reserva');
            const btnLimpar = document.getElementById('btn-limpar-selecao');
            const countBlocos = document.getElementById('count-blocos');
            const containerInputs = document.getElementById('horarios-hidden-inputs');
            const MAX_BLOCOS = 3;

            let colSelecionada = null;
            let rowsSelecionadas = [];

            function atualizarEstadoGrelha() {
                rowsSelecionadas.sort((a, b) => a - b);

                if (rowsSelecionadas.length === 0) {
                    colSelecionada = null;
                }

                const minRow = rowsSelecionadas.length ? rowsSelecionadas[0] : null;
                const maxRow = rowsSelecionadas.length ? rowsSelecionadas[rowsSelecionadas.length - 1] : null;

                botoes.forEach(btn => {
                    const col = parseInt(btn.dataset.col, 10);
                    const row = parseInt(btn.dataset.row, 10);
                    const estaSelecionado = (col === colSelecionada && rowsSelecionadas.includes(row));

                    btn.classList.remove('slot-selected', 'slot-disabled');
                    btn.disabled = false;

                    if (rowsSelecionadas.length === 0) {
                        btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Reservar';
                        return;
                    }

                    if (estaSelecionado) {
                        btn.classList.add('slot-selected');
                        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Selecionado';
                    } else if (rowsSelecionadas.length < MAX_BLOCOS && col === colSelecionada && (row === minRow - 1 || row === maxRow + 1)) {
                        btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> + Seguinte';
                    } else {
                        btn.classList.add('slot-disabled');
                        btn.disabled = true;
                        btn.innerHTML = '<i class="bi bi-dash-circle me-1"></i> Bloqueado';
                    }
                });

                containerInputs.innerHTML = '';
                if (rowsSelecionadas.length > 0) {
                    rowsSelecionadas.forEach(r => {
                        const btn = botoes.find(b => parseInt(b.dataset.col, 10) === colSelecionada && parseInt(b.dataset.row, 10) === r);
                        if (btn) {
                            const hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = 'horarios[]';
                            hidden.value = btn.dataset.horarioId;
                            containerInputs.appendChild(hidden);
                        }
                    });
                    countBlocos.textContent = rowsSelecionadas.length;
                    btnProsseguir.classList.remove('d-none');
                    btnLimpar.classList.remove('d-none');
                } else {
                    btnProsseguir.classList.add('d-none');
                    btnLimpar.classList.add('d-none');
                }
            }

            botoes.forEach(btn => {
                btn.addEventListener('click', () => {
                    const col = parseInt(btn.dataset.col, 10);
                    const row = parseInt(btn.dataset.row, 10);

                    if (rowsSelecionadas.length === 0) {
                        colSelecionada = col;
                        rowsSelecionadas = [row];
                    } else if (col === colSelecionada) {
                        const minRow = rowsSelecionadas[0];
                        const maxRow = rowsSelecionadas[rowsSelecionadas.length - 1];

                        if (rowsSelecionadas.includes(row)) {
                            if (row === minRow || row === maxRow) {
                                rowsSelecionadas = rowsSelecionadas.filter(r => r !== row);
                            }
                        } else if (rowsSelecionadas.length < MAX_BLOCOS && (row === minRow - 1 || row === maxRow + 1)) {
                            rowsSelecionadas.push(row);
                        }
                    }

                    atualizarEstadoGrelha();
                });
            });

            btnLimpar?.addEventListener('click', () => {
                rowsSelecionadas = [];
                atualizarEstadoGrelha();
            });
        });
    </script>
@endpush

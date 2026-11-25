@extends('layouts.app')

@section('title', 'UFP - Cronograma de Reservas')

@section('conteudo')
    <div class="mx-auto" style="max-width: 1100px;">
        <div class="text-center mb-4">
            <h1 class="font-display display-4 fw-bold text-uppercase text-white lh-1 mb-3">
                Cronograma de Reservas
            </h1>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Filtro de Pesquisa -->
                <form method="GET" action="{{ route('reservas.index') }}" class="d-flex gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar..." class="form-control glass-input py-1 px-3 small">
                    <button type="submit" class="btn btn-glass-secondary">Filtrar</button>
                </form>

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
                        <th>Horários</th>
                        <th>Edifício</th>
                        <th>Sala</th>
                        <th>Tipo de Uso</th>
                        <th>Pessoas</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($reservas as $reserva)
                        <tr>
                            <td class="fw-semibold text-white">
                                <i class="bi bi-calendar3 me-2 text-white-50"></i>
                                {{ $reserva->data?->format('d/m/Y') }}
                            </td>
                            <td>
                                @foreach($reserva->horarios as $h)
                                    <span class="badge bg-white bg-opacity-10 text-white me-1">{{ substr($h->hora_inicio, 0, 5) }}</span>
                                @endforeach
                            </td>
                            <td class="text-white-50">
                                {{ $reserva->sala->edificio->nome ?? '—' }}
                            </td>
                            <td class="fw-bold text-white">
                                {{ $reserva->sala->nome_numero ?? '—' }}
                            </td>
                            <td class="text-white-50">
                                {{ $reserva->tipo_de_uso }}
                            </td>
                            <td class="text-white-50">
                                <i class="bi bi-people me-1"></i> {{ $reserva->quantidade_pessoas }}
                            </td>
                            <td class="text-center">
                                <x-badge-status :estado="$reserva->estado" />
                            </td>
                            <td class="text-end">
                                @can('update', $reserva)
                                    <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-sm btn-link text-white-50 p-0 me-2">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan

                                @can('delete', $reserva)
                                    <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja eliminar esta reserva?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-white-50">
                                Não existem reservas registadas de momento.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservas instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="mt-3">
                    {{ $reservas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

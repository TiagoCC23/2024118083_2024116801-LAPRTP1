@extends('layouts.app')

@section('title', 'UFP - Salas')
@section('fundo', asset('images/fundo2.jpg'))

@section('conteudo')
    <div class="mx-auto" style="max-width: 1100px;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h1 class="font-display display-5 fw-bold text-uppercase text-white mb-0">Salas</h1>

            <div class="d-flex flex-wrap gap-2">
                <form action="{{ route('salas.disponibilidade') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="date" name="data" value="{{ date('Y-m-d') }}" class="form-control glass-input py-1 px-2 small" required>
                    <input type="number" name="capacidade_maxima" value="1" min="1" class="form-control glass-input py-1 px-2 small" style="width: 80px;" required>
                    <button type="submit" class="btn btn-glass-secondary">Verificar Disponibilidade</button>
                </form>

                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('salas.create') }}" class="btn btn-minimal">
                        <i class="bi bi-plus-lg me-1"></i> Nova Sala
                    </a>
                @endif
            </div>
        </div>

        <div class="glass-schedule-card p-3">
            <div class="table-responsive">
                <table class="table table-cronograma align-middle">
                    <thead>
                    <tr>
                        <th>Sala</th>
                        <th>Edifício</th>
                        <th>Tipo</th>
                        <th>Piso</th>
                        <th>Capacidade</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($salas as $sala)
                        <tr>
                            <td class="fw-bold text-white">{{ $sala->nome_numero }}</td>
                            <td class="text-white-50">{{ $sala->edificio->nome ?? '—' }}</td>
                            <td class="text-white-50">{{ $sala->tipo_sala }}</td>
                            <td class="text-white-50">{{ $sala->piso }}</td>
                            <td class="text-white-50">{{ $sala->capacidade_maxima }}</td>
                            <td class="text-center"><x-badge-status :estado="$sala->disponibilidade" /></td>
                            <td class="text-end">
                                <a href="{{ route('salas.show', $sala) }}" class="btn btn-sm btn-link text-white-50 p-0 me-2" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()?->isAdmin())
                                    <a href="{{ route('salas.edit', $sala) }}" class="btn btn-sm btn-link text-white-50 p-0 me-2" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('salas.destroy', $sala) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar esta sala?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-white-50">Ainda não existem salas registadas.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($salas instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="mt-3">{{ $salas->links() }}</div>
            @endif
        </div>
    </div>
@endsection

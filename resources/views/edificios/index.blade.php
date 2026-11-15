@extends('layouts.app')

@section('title', 'UFP - Edifícios')
@section('fundo', asset('images/fundo2.jpg'))

@section('conteudo')
    <div class="mx-auto" style="max-width: 1000px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="font-display display-5 fw-bold text-uppercase text-white mb-0">Edifícios</h1>
            @if(auth()->user()?->isAdmin())
                <a href="{{ route('edificios.create') }}" class="btn btn-minimal">
                    <i class="bi bi-plus-lg me-1"></i> Novo Edifício
                </a>
            @endif
        </div>

        <div class="glass-schedule-card p-3">
            <div class="table-responsive">
                <table class="table table-cronograma align-middle">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Sigla</th>
                        <th>Salas</th>
                        <th>Imagem</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($edificios as $edificio)
                        <tr>
                            <td>{{ $edificio->id }}</td>
                            <td class="fw-bold text-white">{{ $edificio->nome }}</td>
                            <td><span class="badge bg-white bg-opacity-10 text-white">{{ $edificio->sigla }}</span></td>
                            <td class="text-white-50">{{ $edificio->salas_count ?? 0 }}</td>
                            <td class="text-white-50">{{ $edificio->imagem ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('edificios.show', $edificio) }}" class="btn btn-sm btn-link text-white-50 p-0 me-2" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()?->isAdmin())
                                    <a href="{{ route('edificios.edit', $edificio) }}" class="btn btn-sm btn-link text-white-50 p-0 me-2" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('edificios.destroy', $edificio) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar este edifício?')">
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
                            <td colspan="6" class="text-center py-4 text-white-50">Ainda não existem edifícios registados.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($edificios instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="mt-3">{{ $edificios->links() }}</div>
            @endif
        </div>
    </div>
@endsection

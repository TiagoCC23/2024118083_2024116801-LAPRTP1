@extends('layouts.app')

@section('title', 'UFP - Avaliações')

@section('conteudo')
    <div class="mx-auto" style="max-width: 1000px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="font-display display-5 fw-bold text-uppercase text-white mb-0">Avaliações</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('avaliacoes.minhas') }}" class="btn btn-glass-secondary">Minhas Avaliações</a>
                <a href="{{ route('avaliacoes.create') }}" class="btn btn-minimal">
                    <i class="bi bi-plus-lg me-1"></i> Nova Avaliação
                </a>
            </div>
        </div>

        <div class="glass-schedule-card p-3">
            <div class="table-responsive">
                <table class="table table-cronograma align-middle">
                    <thead>
                    <tr>
                        <th>Sala</th>
                        <th>Utilizador</th>
                        <th>Nota</th>
                        <th>Comentário</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($avaliacoes as $avaliacao)
                        <tr>
                            <td class="fw-bold text-white">{{ $avaliacao->reserva->sala->nome_numero ?? '—' }}</td>
                            <td class="text-white-50">{{ $avaliacao->reserva->utilizador->nome ?? '—' }}</td>
                            <td class="text-warning fw-bold">{{ $avaliacao->nota }}/5</td>
                            <td class="text-white-50">{{ $avaliacao->comentario ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="btn btn-sm btn-link text-white-50 p-0 me-2" title="Ver"><i class="bi bi-eye"></i></a>

                                @if($avaliacao->reserva?->utilizador_id === auth()->id())
                                    <a href="{{ route('avaliacoes.edit', $avaliacao) }}" class="btn btn-sm btn-link text-white-50 p-0 me-2" title="Editar"><i class="bi bi-pencil"></i></a>
                                @endif

                                @if($avaliacao->reserva?->utilizador_id === auth()->id() || auth()->user()?->isAdmin())
                                    <form action="{{ route('avaliacoes.destroy', $avaliacao) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar esta avaliação?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0" title="Eliminar"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-white-50">Ainda não existem avaliações registadas.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($avaliacoes instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="mt-3">{{ $avaliacoes->links() }}</div>
            @endif
        </div>
    </div>
@endsection

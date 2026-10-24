@extends('layouts.app')
@section('content')

<h1>Lista de avaliações</h1>
<table>
    <tr>
    <th>ID</th>
    <th>Nota</th>
    <th>Comentário</th>
    <th>Reserva_ID</th>
    </tr>

    @foreach($avaliacoes as $avaliacao)
        <tr>
            <td> {{ $avaliacao->id }}</td>
            <td> {{ $avaliacao->nota }}</td>
            <td> {{ $avaliacao->comentario }}</td>
            <td> {{ $avaliacao->reserva_id }}</td>
            <a href="{{route('avaliacoes.edit', $avaliacao)}}">Editar</a>
            <form action="{{ route('avaliacoes.destroy', $avaliacao) }}" method="POST" onsubmit="return confirm('Eliminar esta avaliação')">
                @csrf
                @method('DELETE')   <!---- Pergunta ao server se quer apagar um registo específico dos dados ---->
                <button type="submit">Eliminar</button>
            </form>
        </tr>
    @endforeach
</table>
<br>
<a href="{{ route('avaliacoes.create') }}">Adicionar uma nova avaliação</a>
@endsection

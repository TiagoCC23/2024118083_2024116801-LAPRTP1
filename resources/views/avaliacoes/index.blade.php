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
            <td><button>Editar</button></td> <!--Depois eu tento entender esta sugestão -->
        </tr>
    @endforeach
</table>
<br>
<a href="{{ route('avaliacoes.create') }}">Adicionar uma nova avaliação</a>
@endsection

@extends('layouts.app')
@section('content')

<h1>Lista de Salas</h1>
<table>
    <tr>
    <th>ID</th>
    <th>Nome número</th>
    <th>Capacidade Máxima</th>
    <th>Tipo Sala</th>
    <th>Piso</th>
    <th>Edifício ID</th>
    </tr>

    @foreach($salas as $sala)
        <tr>
            <td> {{ $sala->id }}</td>
            <td> {{ $sala->nome_numero }}</td>
            <td> {{ $sala->capacidade_maxima }}</td>
            <td> {{ $sala->tipo_sala }}</td>
            <td> {{ $sala->disponibilidade }}</td>
            <td> {{ $sala->piso }}</td>
            <td> {{ $sala->edificio_id }}</td>
            <td><button>Editar</button></td>
        </tr>
    @endforeach
</table>
<br>
<a href="{{ route('salas.create') }}">Adicionar um novo edifíco</a>
@endsection

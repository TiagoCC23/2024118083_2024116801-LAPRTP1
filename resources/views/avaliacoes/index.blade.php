@extends('layouts.app')
@section('content')

<h1>Lista de edifícios</h1>
<table>
    <tr>
    <th>ID</th>
    <th>Nome</th>
    <th>Sigla</th>
    <th>Imagem</th>
    </tr>

    @foreach($edificios as $edificio)
        <tr>
            <td> {{ $edificio->id }}</td>
            <td> {{ $edificio->nome }}</td>
            <td> {{ $edificio->sigla }}</td>
            <td> {{ $edificio->imagem }}</td>
            <td><button>Editar</button></td> <!--Depois eu tento entender esta sugestão -->
        </tr>
    @endforeach
</table>
<br>
<a href="{{ route('$edificios.create') }}">Adicionar um novo edifíco</a>
@endsection

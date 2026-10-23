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
            <td>
                <a href="{{route('edificios.edit', $edificio)}}">Editar</a>
                <form action="{{ route('edificios.destroy', $edificio) }}" method="POST" onsubmit="return confirm('Eliminar este edifício')">
                    @csrf
                    @method('DELETE')   <!---- Pergunta ao server se quer apagar um registo específico dos dados ---->
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
<br>
<a href="{{ route('edificios.create') }}">Adicionar um novo edifíco</a>
@endsection

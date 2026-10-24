@extends('layouts.app')
@section('content')
<h1>Detalhes do Salas: {{$sala->nome_numero}}</h1>;

<div>
    <p>ID: {{$sala->id}}</p>
    <p>Nome número: {{$sala->nome_numero}}</p>
    <p>Capacidade Máxima: {{$sala->capacidade_maxima}}</p>
    <p>Tipo Sala: {{$sala->tipo_sala}}</p>
    <p>Disponibilidade: {{$sala->disponibilade}}</p>
    <p>Piso: {{$sala->piso}}</p>
    <p>Edificio ID: {{$sala->edificio_id}}</p>

    <a href="{{route('salas.index')}}">Voltar à lista</a>
    <a href="{{route('salas.edit', $sala->id)}}">Editar este sala</a>
</div>
@endsection

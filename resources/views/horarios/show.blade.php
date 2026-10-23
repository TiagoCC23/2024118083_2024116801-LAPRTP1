@extends('layouts.app')
@section('content')
<h1>Detalhes do Edifício: {{$edificio->nome}}</h1>;

<div>
    <p>ID: {{$edificio->id}}</p>
    <p>Nome: {{$edificio->nome}}</p>
    <p>Sigla: {{$edificio->sigla}}</p>
    <p>Imagem: {{$edificio->imagem}}</p>

    <a href="{{route('edificios.index')}}">Voltar à lista</a>
    <a href="{{route('edificios.edit', $edificio->id)}}">Editar este edifício</a>
</div>
@endsection

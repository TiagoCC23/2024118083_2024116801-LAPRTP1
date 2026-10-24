@extends('layouts.app')
@section('content')
<h1>Detalhes do Edifício: {{$avaliacao->nome}}</h1>;

<div>
    <p>ID: {{$avaliacao->id}}</p>
    <p>Nome: {{$avaliacao->nota}}</p>
    <p>Sigla: {{$avaliacao->comentario}}</p>
    <p>Imagem: {{$avaliacao->reserva_id}}</p>

    <a href="{{route('avaliacoes.index')}}">Voltar à lista</a>
    <a href="{{route('avaliacoes.edit', $avaliacao->id)}}">Editar esta avaliação</a>
</div>
@endsection

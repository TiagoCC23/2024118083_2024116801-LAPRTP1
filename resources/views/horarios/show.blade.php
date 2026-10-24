@extends('layouts.app')
@section('content')
<h1>Detalhes do Horário: {{$horario->id}}</h1>;

<div>
    <p>ID: {{$horario->id}}</p>
    <p>Hora Início: {{$horario->hora_inicio}}</p>
    <p>Hora Fim: {{$horario->hora_fim}}</p>

    <a href="{{route('horarios.index')}}">Voltar à lista</a>
    <a href="{{route('horarios.edit', $horario->id)}}">Editar este horário</a>
</div>
@endsection

@extends('layouts.app')

@section('content')

<h1>Adicionar Novo Horário</h1>

@if ($errors->any())
    <div>
        <strong>Atenção - O formulário contem erros:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('horarios.store') }}" method="POST">
    @csrf                               <!--Impede ataques crossfire -->
@include('horarios._form')
</form>
<br>
<a href="{{ route('horarios.index') }}">Cancelar e Voltar à Lista</a>
@endsection

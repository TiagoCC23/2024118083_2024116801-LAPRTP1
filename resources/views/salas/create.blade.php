@extends('layouts.app')

@section('content')

    <h1>Adicionar Novo Salas</h1>

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

<form action="{{ route('salas.store') }}" method="POST">
    @csrf                               <!--Impede ataques crossfire -->
@include('salas._form')
</form>
<br>
<a href="{{ route('edificios.index') }}">Cancelar e Voltar à Lista</a>
@endsection

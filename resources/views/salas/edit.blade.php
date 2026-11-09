@extends('layouts.app')
@section('content')
<h1>Editar Sala {{$sala->nome_numero}}</h1>

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

<form action="{{ route('salas.store', $sala->id) }}" method="POST">
    @csrf                               <!--Impede ataques crossfire -->

    @method('PUT')                      <!--Update -->

    @include('salas._form')
</form>
<br>
<a href="{{ route('edificios.index') }}">Cancelar e Voltar à Lista</a>
@endsection

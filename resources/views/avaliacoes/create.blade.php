@extends('layouts.app')

@section('content')

<h1>Adicionar Nova Avaliação</h1>

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

<form action="{{ route('avaliacoes.store') }}" method="POST">
    @csrf                               <!--Impede ataques crossfire -->
    <!-- Nota da Avaliação -->
    <div>
        <label for="nome">Nota da avaliação</label><br>
        <input type="number" id="nota" name="nota" value="{{ old('nota')}}" placeholder="0-5">
    </div>

    <!-- Comentário -->
    <div>
        <label for="nome">Comentário</label><br>
        <input type="text" id="comentario" name="comentario" value="{{ old('comentario')}}" placeholder="Comentário">
    </div>

    <!-- Id da reserva -->
    <div>
        <label for="nome">Reserva ID</label><br>
        <input type="number" id="reservaID" name="reservaID" value="{{ old('reservaID')}}" placeholder="ID da reserva">
    </div>

    <button type="submit">Gravar Avaliação</button>
</form>
<br>
<a href="{{ route('avaliacoes.index') }}">Cancelar e Voltar à Lista</a>
@endsection

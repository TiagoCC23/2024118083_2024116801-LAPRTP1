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
    <!-- Nome da Sala -->
    <div>
        <label for="nome">Nome da Sala</label><br>
        <input type="text" id="nome" name="nome" value ="{{ $sala->nome }}" placeholder="Nome">
    </div>

    <!-- Capacidade Maxima -->
    <div>
        <label for="nome">Capacidade Máxima</label><br>
        <input type="text" id="capacidadeMaxima" name="capacidadeMaxima" value ="{{ $sala->capacidade_maxima }}" placeholder="Capacidade Máxima">
    </div>

    <!-- Tipo de Sala -->
    <div>
        <label for="nome">Tipo Sala</label><br>
        <input type="text" id="tipoSala" name="tipoSala" value ="{{ $sala->tipo_sala }}" placeholder="Tipo de Sala">
    </div>

    <!-- Tipo de Sala -->
    <div>
        <label for="nome">Disponibilidade</label><br>
        <input type="text" id="disponibilidade" name="disponibilidade" value ="{{ $sala->disponibilidade }}" placeholder="Disponibilidade">
    </div>

    <!-- Piso -->
    <div>
        <label for="nome">Piso</label><br>
        <input type="text" id="piso" name="piso" value ="{{ $sala->piso }}" placeholder="Piso">
    </div>

    <!-- Edificio ID -->
    <div>
        <label for="nome">Edifício ID</label><br>
        <input type="text" id="edificio_id" name="edifico_id" value ="{{ $sala->edificio_id }}" placeholder="Edifício ID">
    </div>

    <button type="submit">Atualizar Sala</button>
</form>
<br>
<a href="{{ route('edificios.index') }}">Cancelar e Voltar à Lista</a>
@endsection

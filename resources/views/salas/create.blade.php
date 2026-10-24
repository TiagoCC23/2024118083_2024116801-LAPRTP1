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
    <!-- Nome número -->
    <div>
        <label for="nome">Nome número</label><br>
        <input type="text" id="nome_numero" name="nome_numero" value="{{ old('nome_numero')}}" placeholder="Nome número">
    </div>

    <!-- Capacidade Máxima -->
    <div>
        <label for="nome">Capacidade Máxima</label><br>
        <input type="number" id="capacidadeMaxima" name="capacidade" value="{{ old('capacidade')}}" placeholder="Capacidade Máxima">
    </div>

    <!-- Tipo de Sala -->
    <div>
        <label for="nome">Tipo de Sala</label><br>
        <input type="text" id="tipoSala" name="tipoSala" value="{{ old('tipoSala')}}" placeholder="Tipo de Sala">
    </div>

    <!-- Disponibilidade -->
    <div>
        <label for="nome">Disponibilidade </label><br>
        <input type="text" id="disponibilidade" name="disponibilidade" value="{{ old('disponibilidade')}}" placeholder="Disponibilidade">
    </div>

    <!-- Piso -->
    <div>
        <label for="nome">Piso</label><br>
        <input type="number" id="piso" name="piso" value="{{ old('piso')}}" placeholder="piso">
    </div>

    <!-- edificio_id -->
    <div>
        <label for="nome">Edifico_id</label><br>
        <input type="number" id="edificio_id" name="edificio_id" value="{{ old('edificio_id')}}" placeholder="edifico">
    </div>

    <button type="submit">Gravar Sala</button>
</form>
<br>
<a href="{{ route('edificios.index') }}">Cancelar e Voltar à Lista</a>
@endsection

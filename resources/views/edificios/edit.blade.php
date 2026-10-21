@extends('layouts.app')
@section('content')
<h1>Editar Edifício {{$edificio->nome}}</h1>

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

<form action="{{ route('edificio.update', $edificio->id) }}" method="POST">
    @csrf                               <!--Impede ataques crossfire -->

    @method('PUT')                      <!--Update -->
    <!-- Nome do Edifício -->
    <div>
        <label for="nome">Nome do Edifício</label><br>
        <input type="text" id="nome" name="nome" value ="{{ $edificio->nome }}" placeholder="Nome">
    </div>

    <!-- Sigla do Edifício -->
    <div>
        <label for="nome">Sigla</label><br>
        <input type="text" id="sigla" name="sigla" value ="{{ $edificio->sigla }}" placeholder="Sigla">
    </div>

    <!-- Planta/Imagem do edifício -->
    <div>
        <label for="nome">Nome da imagem</label><br>
        <input type="text" id="imagem" name="imagem" value ="{{ $edificio->imagem }}" placeholder="Nome do ficheiro da imagem">
    </div>

    <button type="submit">Atualizar Edifício</button>
</form>
<br>
<a href="{{ route('edificios.index') }}">Cancelar e Voltar à Lista</a>
@endsection

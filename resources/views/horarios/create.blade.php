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
    <!-- Hora início -->
    <div>
        <label for="nome">Hora início</label><br>
        <input type="text" id="horaInicio" name="horaInicio" value="{{ old('horaInicio')}}" placeholder="Hora início">
    </div>

    <!-- Hora fim -->
    <div>
        <label for="nome">Hora fim</label><br>
        <input type="text" id="horaFim" name="horaFim" value="{{ old('horaFim')}}" placeholder="Hora fim">
    </div>

    <button type="submit">Gravar Horário</button>
</form>
<br>
<a href="{{ route('horarios.index') }}">Cancelar e Voltar à Lista</a>
@endsection

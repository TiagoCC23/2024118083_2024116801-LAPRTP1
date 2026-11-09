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
@include('avaliacoes._form')
</form>
<br>
<a href="{{ route('avaliacoes.index') }}">Cancelar e Voltar à Lista</a>
@endsection

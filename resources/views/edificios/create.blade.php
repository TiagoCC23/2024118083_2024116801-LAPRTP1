@extends('layouts.app')
@section('content')
<h1>Adicionar Novo Edifício</h1>

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

<form action="{{ route('edificios.store') }}" method="POST">
    @csrf                               <!----Impede ataques crossfire ---->
@include('edificios._form')
</form>
<br>
<a href="{{ route('edificios.index') }}">Cancelar e Voltar à Lista</a>
@endsection

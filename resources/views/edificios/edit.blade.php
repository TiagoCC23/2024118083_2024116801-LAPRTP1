@extends('layouts.app')

@section('title', 'UFP - Editar Edifício')
@section('fundo', asset('images/fundo2.jpg'))

@section('conteudo')
    <div class="liquid-glass-card p-4 p-md-5 mx-auto" style="max-width: 700px;">
        <h2 class="font-display text-uppercase text-white fw-bold mb-4">Editar Edifício: {{ $edificio->nome }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-25 border-danger text-white small mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('edificios.update', $edificio) }}" method="POST">
            @csrf
            @method('PUT')
            @include('edificios._form')
        </form>
    </div>
@endsection

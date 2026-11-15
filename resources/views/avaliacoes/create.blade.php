@extends('layouts.app')

@section('title', 'UFP - Nova Avaliação')

@section('conteudo')
    <div class="liquid-glass-card p-4 p-md-5 mx-auto" style="max-width: 700px;">
        <h2 class="font-display text-uppercase text-white fw-bold mb-4">Adicionar Nova Avaliação</h2>

        @if ($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-25 border-danger text-white small mb-4">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('avaliacoes.store') }}" method="POST">
            @csrf
            @include('avaliacoes._form')
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'UFP - Editar Reserva')
@section('fundo', asset('images/fundo4.webp'))

@section('conteudo')
    <div class="liquid-glass-card p-4 p-md-5 mx-auto" style="max-width: 850px;">
        <h3 class="font-display text-uppercase text-white fw-bold mb-1">Editar Reserva #{{ $reserva->id }}</h3>
        <p class="text-white-50 small mb-4">Altere os detalhes da reserva abaixo.</p>

        @if ($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-25 border-danger text-white mb-4 small rounded-3">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul>
            </div>
        @endif

        @include('reservas._form')
    </div>
@endsection

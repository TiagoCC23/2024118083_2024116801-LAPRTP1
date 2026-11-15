@extends('layouts.app')

@section('title', 'UFP - Verificar Email')

@section('conteudo')
    <div class="liquid-glass-card p-4 p-md-5 mx-auto text-center" style="max-width: 560px;">
        <h2 class="font-display text-uppercase text-white fw-bold mb-3">Verifique o seu email</h2>
        <p class="text-white-50 small mb-4">
            Antes de continuar, confirme o seu endereço de email através do link que enviámos.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-liquid">
                Reenviar Email de Verificação
            </button>
        </form>
    </div>
@endsection

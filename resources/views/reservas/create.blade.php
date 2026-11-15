@extends('layouts.app')

@section('title', 'UFP - Nova Reserva')

@section('conteudo')
    <div class="row g-4 align-items-start mx-auto" style="max-width: 1150px;">

        <!-- Lado Esquerdo: Data Selecionada -->
        <div class="col-lg-4">
            <div class="liquid-glass-card p-4 text-center">
            <span class="text-uppercase text-white-50 small fw-bold mb-2 d-block" style="letter-spacing: 0.2em;">
                Dia Selecionado
            </span>

                @php
                    $dataString = $dataSelecionada ?? request('data', date('Y-m-d'));
                    $carbonData = \Carbon\Carbon::parse($dataString)->locale('pt');
                @endphp

                <div class="font-display my-2" style="font-size: 3.5rem; font-weight: 800;">
                    {{ $carbonData->format('d') }}
                </div>

                <div class="text-uppercase fw-bold text-white small" style="letter-spacing: 0.15em;">
                    {{ $carbonData->translatedFormat('F') }}
                </div>

                <div class="text-white-50 small">
                    {{ $carbonData->format('Y') }} · {{ ucfirst($carbonData->translatedFormat('l')) }}
                </div>

                <div class="mt-4 pt-3 border-top border-white border-opacity-10 w-100">
                    <a href="{{ url('/home') }}" class="text-white-50 text-decoration-none small text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Alterar Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Lado Direito: Formulário -->
        <div class="col-lg-8">
            <div class="liquid-glass-card p-4 p-md-5">
                <h3 class="font-display text-uppercase text-white fw-bold mb-1">
                    Efetuar Reserva
                </h3>
                <p class="text-white-50 small mb-4">
                    Preencha os detalhes abaixo para concluir o seu agendamento.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger bg-danger bg-opacity-20 border-danger text-white mb-4 small rounded-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $erro)
                                <li>{{ $erro }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @include('reservas._form')
            </div>
        </div>

    </div>
@endsection

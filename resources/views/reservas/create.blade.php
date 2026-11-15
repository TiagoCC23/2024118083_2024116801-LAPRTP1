<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UFP - Nova Reserva</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Plus+Jakarta+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @extends('layouts.app')

@section('title', 'UFP - Nova Reserva')

@section('conteudo')
<div class="row g-4 align-items-start mx-auto" style="max-width: 1150px;">

        <!-- CARD DA DATA SELECIONADA -->
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

        <!-- FORMULÁRIO -->
        <div class="col-lg-8">
        <div class="liquid-glass-card p-4 p-md-5">
        <h3 class="font-display text-uppercase text-white fw-bold mb-1">
                                                                       Efetuar Reserva
        </h3>
        <p class="text-white-50 small mb-4">
                                           Preencha os detalhes abaixo para concluir o seu agendamento.
        </p>

        @include('reservas._form')
</div>
        </div>

        </div>
        @endsection
    </style>
</head>
<body class="font-body min-vh-100 d-flex flex-column justify-content-between">

<!-- ================= 1. HEADER  ================= -->
<header class="p-4 p-md-5 d-flex justify-content-between align-items-center position-relative z-3">
    <a href="{{ url('/home') }}" class="text-white text-decoration-none font-display fs-4 fw-bold tracking-tight">
        UFP <span class="fw-normal text-white-50">SALAS</span>
    </a>

    <div class="d-flex align-items-center gap-3">
        @auth
            <span class="text-white-50 small d-none d-sm-inline">Olá, <strong>{{ Auth::user()->nome ?? Auth::user()->name }}</strong></span>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link text-white-50 text-decoration-none text-uppercase small p-0">
                    Sair <i class="bi bi-box-arrow-right ms-1"></i>
                </button>
            </form>
        @endauth
    </div>
</header>

<!-- ================= 2. CONTEÚDO CENTRAL ================= -->
<main class="container my-auto py-5 position-relative z-3" style="max-width: 1150px;">

    <div class="row g-4 align-items-start">

        <!-- LADO ESQUERDO: QUADRADO "DIA SELECIONADO" -->
        <div class="col-lg-4">
            <div class="liquid-glass-card date-display-box">
            <span class="text-uppercase text-white-50 small fw-bold mb-2" style="letter-spacing: 0.2em;">
                Dia Selecionado
            </span>

                @php
                    // Captura a data da request ou usa a data atual como fallback
                    $dataString = $dataSelecionada ?? request('data', date('Y-m-d'));
                    $carbonData = \Carbon\Carbon::parse($dataString)->locale('pt');
                @endphp

                <div class="date-number my-2">
                    {{ $carbonData->format('d') }}
                </div>

                <div class="text-uppercase fw-bold text-white small" style="letter-spacing: 0.15em;">
                    {{ $carbonData->translatedFormat('F') }}
                </div>

                <div class="text-white-50 small">
                    {{ $carbonData->format('Y') }} · {{ ucfirst($carbonData->translatedFormat('l')) }}
                </div>

                <div class="mt-4 pt-3 border-top border-white-10 w-100">
                    <a href="{{ url('/home') }}" class="text-white-50 text-decoration-none small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                        <i class="bi bi-arrow-left me-1"></i> Alterar Data
                    </a>
                </div>
            </div>
        </div>

        <!-- LADO DIREITO: RETÂNGULO MAIOR "FORMS" -->
        <div class="col-lg-8">
            <div class="liquid-glass-card p-4 p-md-5">

                <h3 class="font-display text-uppercase text-white fw-bold mb-1">
                    Efetuar Reserva
                </h3>
                <p class="text-white-50 small mb-4">
                    Preencha os detalhes abaixo para concluir o seu agendamento.
                </p>

                @include('reservas._form')
            </div>
        </div>
    </div>
</main>

<!-- ================= 3. FOOTER ================= -->
<footer class="p-4 text-center text-white-50 text-uppercase position-relative z-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
    © {{ date('Y') }} Universidade Fernando Pessoa · Gestão de Espaços
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

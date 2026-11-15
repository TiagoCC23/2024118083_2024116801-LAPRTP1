<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UFP - Gestão de Espaços')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Plus+Jakarta+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS, Icons & Flatpickr -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.72), rgba(0, 0, 0, 0.72)),
            url('@yield("fundo", asset($fundo ?? "images/fundo1.jpg"))') center / cover no-repeat fixed !important;
            color: #ffffff !important;
            min-height: 100vh;
        }
    </style>
    @stack('styles')
</head>
<body class="font-body d-flex flex-column justify-content-between min-vh-100">

@hasSection('header')
    @yield('header')
@else
    <header class="glass-header border-bottom p-4 px-md-5 d-flex justify-content-between align-items-center position-relative z-3">
        <a href="{{ route('home') }}" class="text-white text-decoration-none font-display fs-4 fw-bold tracking-tight">
            UFP <span class="fw-normal text-white-50">SALAS</span>
        </a>

        @auth
            <nav class="d-none d-md-flex align-items-center gap-2">
                <a href="{{ route('home') }}" class="nav-glass-link {{ request()->routeIs('home') ? 'active' : '' }}">Início</a>
                <a href="{{ route('reservas.index') }}" class="nav-glass-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">Reservas</a>
                <a href="{{ route('horarios.index') }}" class="nav-glass-link {{ request()->routeIs('horarios.*') ? 'active' : '' }}">Horários</a>
                <a href="{{ route('salas.index') }}" class="nav-glass-link {{ request()->routeIs('salas.*') ? 'active' : '' }}">Salas</a>
                <a href="{{ route('edificios.index') }}" class="nav-glass-link {{ request()->routeIs('edificios.*') ? 'active' : '' }}">Edifícios</a>
                <a href="{{ route('avaliacoes.index') }}" class="nav-glass-link {{ request()->routeIs('avaliacoes.*') ? 'active' : '' }}">Avaliações</a>
            </nav>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small d-none d-sm-inline">
                    Olá, <strong>{{ Auth::user()->nome }}</strong>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-white-50 text-decoration-none text-uppercase small p-0">
                        Sair <i class="bi bi-box-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
        @endauth
    </header>
@endif

<main class="container my-auto py-4 position-relative z-3">
    @if ($msg = session('ok') ?? session('success') ?? session('message'))
        <div class="alert alert-success bg-success bg-opacity-25 border-success text-white mb-4 mx-auto" style="max-width: 1100px;">
            {{ $msg }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger bg-danger bg-opacity-25 border-danger text-white mb-4 mx-auto" style="max-width: 1100px;">
            {{ session('error') }}
        </div>
    @endif

    @yield('conteudo')
</main>

<footer class="glass-footer border-top p-4 text-center text-white-50 text-uppercase position-relative z-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
    © {{ date('Y') }} Universidade Fernando Pessoa · Gestão de Espaços
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

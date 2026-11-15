<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UFP - Salas de Estudo')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Plus+Jakarta+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Blindagem para garantir que o Bootstrap nunca force o fundo branco */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)),
            url('{{ asset($fundo ?? "images/fundo1.jpg") }}') center / cover no-repeat fixed !important;
            color: #ffffff !important;
            min-height: 100vh;
        }
    </style>
    @stack('styles')
</head>
<body class="font-body d-flex flex-column justify-content-between min-vh-100">

<!-- CABEÇALHO -->
@hasSection('header')
    @yield('header')
@else
    <header class="glass-header border-bottom p-4 px-md-5 d-flex justify-content-between align-items-center position-relative z-3">
        <a href="{{ url('/home') }}" class="text-white text-decoration-none font-display fs-4 fw-bold tracking-tight">
            UFP <span class="fw-normal text-white-50">SALAS</span>
        </a>

        @auth
            <nav class="d-none d-md-flex align-items-center gap-2">
                <a href="{{ route('reservas.index') }}" class="nav-glass-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">Reservas</a>
                <a href="{{ route('horarios.index') }}" class="nav-glass-link {{ request()->routeIs('horarios.*') ? 'active' : '' }}">Horários</a>
                <a href="{{ route('salas.index') }}" class="nav-glass-link {{ request()->routeIs('salas.*') ? 'active' : '' }}">Salas</a>
                <a href="{{ route('edificios.index') }}" class="nav-glass-link {{ request()->routeIs('edificios.*') ? 'active' : '' }}">Edifícios</a>
            </nav>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small">Olá, <strong>{{ Auth::user()->nome ?? Auth::user()->name }}</strong></span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-white-50 text-decoration-none text-uppercase small p-0">Sair</button>
                </form>
            </div>
        @endauth
    </header>
@endif

<!-- CONTEÚDO PRINCIPAL -->
<main class="container my-auto py-4 position-relative z-3">
    @if (session('ok'))
        <div class="alert alert-success bg-success bg-opacity-20 border-success text-white mb-4">
            {{ session('ok') }}
        </div>
    @endif

    @yield('conteudo')
</main>

<!-- RODAPÉ -->
<footer class="p-4 text-center text-white-50 text-uppercase position-relative z-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
    © {{ date('Y') }} UNIVERSIDADE FERNANDO PESSOA · GESTÃO DE ESPAÇOS
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

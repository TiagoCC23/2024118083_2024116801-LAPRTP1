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

        .navbar-collapse,
        .navbar-collapse.collapse {
            visibility: visible !important;
        }

        .glass-header,
        .glass-footer {
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .nav-glass-link {
            color: rgba(255, 255, 255, 0.75) !important;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.45rem 0.8rem !important;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-glass-link:hover,
        .nav-glass-link.active {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.14);
        }

        .dropdown-menu-glass {
            background: rgba(18, 18, 22, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.75rem;
        }

        .dropdown-menu-glass .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
        }

        .dropdown-menu-glass .dropdown-item:hover,
        .dropdown-menu-glass .dropdown-item:focus {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body class="font-body d-flex flex-column justify-content-between min-vh-100">

@hasSection('header')
    @yield('header')
@else
    <nav class="navbar navbar-expand-lg navbar-dark glass-header border-bottom px-3 px-md-5 py-3 position-relative z-3">
        <div class="container-fluid px-0">
            <a href="{{ route('home') }}" class="navbar-brand text-white font-display fs-4 fw-bold tracking-tight mb-0">
                UFP <span class="fw-normal text-white-50">SALAS</span>
            </a>

            @auth
                <button class="navbar-toggler border-white border-opacity-25" type="button"
                        data-bs-toggle="collapse" data-bs-target="#navbarPrincipal"
                        aria-controls="navbarPrincipal" aria-expanded="false" aria-label="Alternar navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarPrincipal">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 align-items-lg-center">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link nav-glass-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                <i class="bi bi-house me-1"></i> Início
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('edificios.index') }}" class="nav-link nav-glass-link {{ request()->routeIs('edificios.*') ? 'active' : '' }}">
                                <i class="bi bi-building me-1"></i> Edifícios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('salas.index') }}" class="nav-link nav-glass-link {{ request()->routeIs('salas.index', 'salas.show', 'salas.create', 'salas.edit') ? 'active' : '' }}">
                                <i class="bi bi-door-open me-1"></i> Salas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('salas.disponibilidade') }}" class="nav-link nav-glass-link {{ request()->routeIs('salas.disponibilidade') ? 'active' : '' }}">
                                <i class="bi bi-search me-1"></i> Disponibilidade
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('horarios.index') }}" class="nav-link nav-glass-link {{ request()->routeIs('horarios.*') ? 'active' : '' }}">
                                <i class="bi bi-clock me-1"></i> Horários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reservas.index') }}" class="nav-link nav-glass-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">
                                <i class="bi bi-calendar-check me-1"></i> Reservas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('avaliacoes.index') }}" class="nav-link nav-glass-link {{ request()->routeIs('avaliacoes.*') ? 'active' : '' }}">
                                <i class="bi bi-star me-1"></i> Avaliações
                            </a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-3 mt-2 mt-lg-0">
                        <div class="dropdown">
                            <button class="btn btn-link text-white-50 text-decoration-none small dropdown-toggle p-0"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                Olá, <strong class="text-white">{{ Auth::user()->nome }}</strong>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-glass shadow mt-2">
                                <li>
                                    <a class="dropdown-item" href="{{ route('reservas.create') }}">
                                        <i class="bi bi-plus-circle me-2"></i> Nova Reserva
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('reservas.minhas') }}">
                                        <i class="bi bi-journal-bookmark me-2"></i> Minhas Reservas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('avaliacoes.minhas') }}">
                                        <i class="bi bi-star-half me-2"></i> Minhas Avaliações
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider border-white border-opacity-10"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </nav>
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

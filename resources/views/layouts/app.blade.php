<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestão de Salas UFP')</title>

    <!-- Google Fonts (Syne e Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Plus+Jakarta+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Flatpickr (Calendário) CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-display {
            font-family: 'Syne', sans-serif;
            letter-spacing: -0.04em;
        }
        .font-body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Fundo Geral da Aplicação */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)),
            url("{{ asset('images/fundo3.jpg') }}") center / cover no-repeat fixed;
            color: #ffffff;
        }

        /* Header e Nav estilo Liquid Glass */
        .glass-header {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        /* Links do Menu de Navegação */
        .nav-glass-link {
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            padding: 0.5rem 0.8rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .nav-glass-link:hover, .nav-glass-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Footer Liquid Glass */
        .glass-footer {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }
    </style>

    @stack('styles')
</head>
<body class="font-body min-vh-100 d-flex flex-column justify-content-between">

<!-- ================= 1. HEADER & CABEÇALHO DE NAVEGAÇÃO ================= -->
<header class="glass-header sticky-top position-relative z-3">
    <div class="container-fluid px-4 px-md-5 py-3">
        <div class="d-flex justify-content-between align-items-center">

            <!-- Logo / Brand -->
            <a href="{{ url('/') }}" class="text-white text-decoration-none font-display fs-4 fw-bold tracking-tight me-4">
                UFP <span class="fw-normal text-white-50">SALAS</span>
            </a>

            <!-- Menu de Navegação Central -->
            <nav class="d-none d-lg-flex align-items-center gap-1">
                <a href="{{ route('edificios.index') }}" class="nav-glass-link {{ request()->routeIs('edificios.*') ? 'active' : '' }}">
                    <i class="bi bi-building me-1"></i> Edifícios
                </a>
                <a href="{{ route('salas.index') }}" class="nav-glass-link {{ request()->routeIs('salas.*') ? 'active' : '' }}">
                    <i class="bi bi-door-open me-1"></i> Salas
                </a>
                <a href="{{ route('reservas.index') }}" class="nav-glass-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text me-1"></i> Reservas
                </a>
                <a href="{{ route('horarios.index') }}" class="nav-glass-link {{ request()->routeIs('horarios.*') ? 'active' : '' }}">
                    <i class="bi bi-clock me-1"></i> Horários
                </a>

                @if(Route::has('utilizadores.index'))
                    <a href="{{ route('utilizadores.index') }}" class="nav-glass-link {{ request()->routeIs('utilizadores.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-1"></i> Utilizadores
                    </a>
                @endif

                @if(Route::has('avaliacoes.index'))
                    <a href="{{ route('avaliacoes.index') }}" class="nav-glass-link {{ request()->routeIs('avaliacoes.*') ? 'active' : '' }}">
                        <i class="bi bi-star me-1"></i> Avaliações
                    </a>
                @endif
            </nav>

            <!-- Área do Utilizador (Direita) -->
            <div class="d-flex align-items-center gap-3">
                @auth
                    <div class="d-flex align-items-center text-white-50 small gap-2">
                        <i class="bi bi-person-circle fs-5 text-white"></i>
                        <span class="d-none d-sm-inline fw-semibold text-white">{{ Auth::user()->nome ?? Auth::user()->name }}</span>
                    </div>
                    <span class="text-white-50">|</span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-white-50 text-decoration-none text-uppercase small p-0" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                            Sair <i class="bi bi-box-arrow-right ms-1"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-glass-link">
                        Entrar <i class="bi bi-box-arrow-in-right ms-1"></i>
                    </a>
                @endauth
            </div>

        </div>
    </div>
</header>

<!-- ================= 2. CONTEÚDO PRINCIPAL ================= -->
<main class="container my-auto py-4 position-relative z-3">

    <!-- Alertas Globais do Laravel (Sucesso/Erros) -->
    @if (session('success') || session('status'))
        <div class="alert alert-success bg-success bg-opacity-20 border-success border-opacity-50 text-white rounded-3 mb-4 text-xs">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') ?? session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger bg-danger bg-opacity-20 border-danger border-opacity-50 text-white rounded-3 mb-4 text-xs">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Inserção de Conteúdo das Subviews -->
    @yield('content')

</main>

<!-- ================= 3. RODAPÉ (FOOTER) ================= -->
<footer class="glass-footer p-4 text-center text-white-50 text-uppercase position-relative z-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
    <div class="container">
        <p class="mb-0">© {{ date('Y') }} Universidade Fernando Pessoa · Gestão de Espaços e Lotação</p>
    </div>
</footer>

<!-- JS do Bootstrap & Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/pt.js"></script>

@stack('scripts')
</body>
</html>

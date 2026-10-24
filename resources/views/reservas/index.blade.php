<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UFP - Cronograma de Reservas</title>

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
        .font-display {
            font-family: 'Syne', sans-serif;
            letter-spacing: -0.04em;
        }
        .font-body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Estilo Dark com Imagem de Fundo */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)),
            url("{{ asset('images/fundo1.jpg') }}") center / cover no-repeat fixed;
            color: #ffffff;
        }

        /* Card Liquid Glassmorphism para a Tabela */
        .glass-schedule-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1.5rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        /* Tabela Estilizada Dark */
        .table-cronograma {
            color: rgba(255, 255, 255, 0.85) !important;
            margin-bottom: 0;
            vertical-align: middle;
        }

        .table-cronograma th {
            background: rgba(255, 255, 255, 0.05) !important;
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 0.7rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.12em !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
            padding: 1rem 1.25rem !important;
        }

        .table-cronograma td {
            background: transparent !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding: 1rem 1.25rem !important;
            font-size: 0.85rem;
        }

        .table-cronograma tbody tr {
            transition: all 0.2s ease;
        }

        .table-cronograma tbody tr:hover {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        /* Badges de Estado */
        .badge-status {
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.35rem 0.75rem;
            border-radius: 50rem;
            font-weight: 600;
        }
        .badge-confirmada {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }
        .badge-pendente {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }
        .badge-cancelada {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.3);
        }

        /* Botão Minimalista */
        .btn-minimal {
            background-color: #ffffff;
            color: #000000;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            padding: 0.6rem 1.5rem;
            border-radius: 50rem;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-minimal:hover {
            background-color: #e2e8f0;
            color: #000000;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="font-body min-vh-100 d-flex flex-column justify-content-between">

<!-- Cabeçalho -->
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

<!-- Conteúdo Central -->
<main class="container my-auto py-5 position-relative z-3" style="max-width: 1100px;">
    <div class="text-center mb-4">

        <!-- TÍTULO PRINCIPAL -->
        <h1 class="font-display display-3 fw-bold text-uppercase text-white lh-1 mb-2 select-none">
            CRONOGRAMA DE RESERVAS
        </h1>

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('reservas.create') }}" class="btn btn-minimal">
                <i class="bi bi-plus-lg me-1"></i> Nova Reserva
            </a>
        </div>
    </div>

    <!-- TABELA CRONOGRAMA (GLASS CARD) -->
    <div class="glass-schedule-card p-2 p-md-3">
        <div class="table-responsive">
            <table class="table table-cronograma align-middle">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Edifício</th>
                    <th>Sala</th>
                    <th>Tipo de Uso</th>
                    <th>Nº Pessoas</th>
                    <th class="text-center">Estado</th>
                </tr>
                </thead>
                <tbody>
                {{-- Loop para ler os dados do CSV passados pela Controller --}}
                @forelse ($reservasCsv ?? [] as $linha)
                    <tr>
                        <td class="fw-semibold text-white">
                            <i class="bi bi-calendar3 me-2 text-white-50"></i>
                            {{ $linha['data'] ?? '—' }}
                        </td>
                        <td>
                            <i class="bi bi-clock me-1 text-white-50"></i>
                            {{ $linha['horario'] ?? '—' }}
                        </td>
                        <td class="text-white-50">
                            {{ $linha['edificio'] ?? '—' }}
                        </td>
                        <td class="fw-bold text-white">
                            {{ $linha['sala'] ?? '—' }}
                        </td>
                        <td class="text-white-50">
                            {{ $linha['tipo_de_uso'] ?? 'Estudo' }}
                        </td>
                        <td class="text-white-50">
                            <i class="bi bi-people me-1"></i> {{ $linha['quantidade_pessoas'] ?? 1 }}
                        </td>
                        <td class="text-center">
                            @php
                                $estado = strtolower($linha['estado'] ?? 'pendente');
                                $badgeClass = match($estado) {
                                    'confirmada' => 'badge-confirmada',
                                    'cancelada', 'rejeitada' => 'badge-cancelada',
                                    default => 'badge-pendente',
                                };
                            @endphp
                            <span class="badge-status {{ $badgeClass }}">
                                    {{ ucfirst($estado) }}
                                </span>
                        </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="p-4 text-center text-white-50 text-uppercase position-relative z-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
    © {{ date('Y') }} Universidade Fernando Pessoa · Gestão de Espaços
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

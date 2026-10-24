<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UFP - Horário Semanal</title>

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

        /* Fundo Geral */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)),
            url("{{ asset('images/fundo3.jpg') }}") center / cover no-repeat fixed;
            color: #ffffff;
        }

        /* Card Liquid Glass */
        .liquid-glass-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1.5rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Tabela Matriz Semanal */
        .table-weekly {
            color: rgba(255, 255, 255, 0.85) !important;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        /* Cabeçalho dos Dias da Semana */
        .table-weekly th.header-day {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.12em !important;
            text-align: center;
            padding: 1rem 0.5rem !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        /* Coluna Lateral das Horas */
        .table-weekly td.header-hour {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 0.75rem;
            text-align: center;
            vertical-align: middle;
            width: 100px;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        /* Células de Conteúdo */
        .table-weekly td.slot-cell {
            background: rgba(255, 255, 255, 0.02) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            height: 60px;
            vertical-align: middle;
            text-align: center;
            padding: 0.4rem !important;
            transition: all 0.2s ease;
        }

        .table-weekly td.slot-cell:hover {
            background: rgba(255, 255, 255, 0.08) !important;
        }

        /* Item de Reserva dentro do Slot */
        .slot-item {
            display: block;
            padding: 0.3rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s ease;
        }
        .slot-item:hover {
            transform: scale(1.03);
        }

        .slot-free {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .slot-busy {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.3);
            cursor: not-allowed;
        }
    </style>
</head>
<body class="font-body min-vh-100 d-flex flex-column justify-content-between">

<!-- CABEÇALHO -->
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

<!-- CONTEÚDO CENTRAL -->
<main class="container-fluid px-3 px-md-5 my-auto py-4 position-relative z-3">

    <!-- PAINEL SUPERIOR: CRONOGRAMA SEMANAL -->
    <div class="liquid-glass-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="font-display text-uppercase text-white fw-bold mb-1">
                    Horário Semanal
                </h2>
                <p class="text-white-50 small mb-0">
                    Visão geral da disponibilidade de salas para esta semana.
                </p>
            </div>

            <!-- Navegação de Semana -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-white-50 small text-uppercase" style="letter-spacing: 0.1em;">Semana de:</span>
                <span class="badge bg-white text-dark fs-6 px-3 py-2 fw-bold rounded-pill">
                    {{ date('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- GRADE MATRIZ ESTILO SCHEDULE -->
    <div class="liquid-glass-card p-3 p-md-4">
        <div class="table-responsive">
            <table class="table table-weekly align-middle">
                <thead>
                <tr>
                    <th class="header-day" style="width: 100px;">Hora</th>
                    <th class="header-day">Segunda</th>
                    <th class="header-day">Terça</th>
                    <th class="header-day">Quarta</th>
                    <th class="header-day">Quinta</th>
                    <th class="header-day">Sexta</th>
                    <th class="header-day">Sábado</th>
                    <th class="header-day">Domingo</th>
                </tr>
                </thead>
                <tbody>
                @php
                    // Lista das Horas (de 08:00 a 20:00)
                    $horas = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
                    $diasSemana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
                @endphp

                @foreach($horas as $hora)
                    <tr>
                        <!-- Coluna Lateral Vermelha/Destacada com a Hora -->
                        <td class="header-hour">
                            {{ $hora }}
                        </td>

                        <!-- Células dos Dias da Semana -->
                        @foreach($diasSemana as $dia)
                            @php
                                // Exemplo de simulação de disponibilidade por slot
                                $isOcupado = ($hora == '11:00' && $dia == 'Quarta') || ($hora == '15:00' && $dia == 'Sexta');
                            @endphp

                            <td class="slot-cell">
                                @if($isOcupado)
                                    <span class="slot-item slot-busy">
                                            <i class="bi bi-x-circle me-1"></i> Ocupado
                                        </span>
                                @else
                                    <a href="{{ route('reservas.create', ['horario' => $hora, 'dia' => $dia]) }}" class="slot-item slot-free">
                                        <i class="bi bi-plus-circle me-1"></i> Livre
                                    </a>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</main>

<!-- RODAPÉ -->
<footer class="p-4 text-center text-white-50 text-uppercase position-relative z-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
    © {{ date('Y') }} Universidade Fernando Pessoa · Gestão de Espaços
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UFP - Grade de Horários</title>

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

        /* Tabela Estilizada Dark */
        .table-horarios {
            color: rgba(255, 255, 255, 0.85) !important;
            margin-bottom: 0;
            vertical-align: middle;
        }

        .table-horarios th {
            background: rgba(255, 255, 255, 0.05) !important;
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 0.7rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.12em !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
            padding: 1rem 1.25rem !important;
        }

        .table-horarios td {
            background: transparent !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding: 1rem 1.25rem !important;
            font-size: 0.85rem;
        }

        .table-horarios tbody tr {
            transition: all 0.2s ease;
        }

        .table-horarios tbody tr:hover {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        /* Badges de Lotação / Ocupação */
        .badge-status {
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.35rem 0.75rem;
            border-radius: 50rem;
            font-weight: 600;
        }
        .badge-livre {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }
        .badge-ocupado {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.3);
        }

        /* Botão Ação Liquid */
        .btn-liquid-sm {
            background-color: #ffffff;
            color: #000000;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.7rem;
            padding: 0.4rem 1rem;
            border-radius: 50rem;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-liquid-sm:hover {
            background-color: #e2e8f0;
            color: #000000;
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

<!-- ================= 2. CONTEÚDO CENTRAL ================= -->
<main class="container my-auto py-5 position-relative z-3" style="max-width: 1000px;">

    <!-- PAINEL SUPERIOR: MESMAS INFORMAÇÕES QUE NA RESERVA -->
    <div class="liquid-glass-card p-4 p-md-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <span class="badge bg-white bg-opacity-10 text-white border border-white-20 rounded-pill px-3 py-1 text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.15em;">
                    Universidade Fernando Pessoa
                </span>
                <h3 class="font-display text-uppercase text-white fw-bold mb-1">
                    Grade Horária de Disponibilidade
                </h3>
                <p class="text-white-50 small mb-0">
                    Consulte os slots de 1 hora para planeamento das salas de estudo.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                @php
                    $dataString = request('data', date('Y-m-d'));
                    $carbonData = \Carbon\Carbon::parse($dataString)->locale('pt');
                @endphp
                <div class="d-inline-block text-start text-md-end border-start border-md-0 ps-3 ps-md-0">
                    <span class="text-uppercase text-white-50 small d-block" style="font-size: 0.65rem; letter-spacing: 0.1em;">Data Selecionada</span>
                    <span class="fw-bold text-white fs-5">{{ $carbonData->format('d/m/Y') }}</span>
                    <span class="text-white-50 small d-block">({{ ucfirst($carbonData->translatedFormat('l')) }})</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TABELA DE HORÁRIOS -->
    <div class="liquid-glass-card p-3 p-md-4">
        <div class="table-responsive">
            <table class="table table-horarios align-middle">
                <thead>
                <tr>
                    <th>Horário</th>
                    <th>Disponibilidade</th>
                    <th>Sala</th>
                    <th>Tipo de Uso</th>
                    <th class="text-end">Ação</th>
                </tr>
                </thead>
                <tbody>
                @php
                    // Lista estática/dinâmica com os campos ajustados
                    $blocosHorarios = $horarios ?? [
                        ['inicio' => '08:00', 'fim' => '09:00', 'estado' => 'Disponível', 'sala' => 'Sala 101', 'tipo_uso' => 'Estudo Individual'],
                        ['inicio' => '09:00', 'fim' => '10:00', 'estado' => 'Disponível', 'sala' => 'Sala 102', 'tipo_uso' => 'Trabalho de Grupo'],
                        ['inicio' => '10:00', 'fim' => '11:00', 'estado' => 'Disponível', 'sala' => 'Sala 201', 'tipo_uso' => 'Estudo Individual'],
                        ['inicio' => '11:00', 'fim' => '12:00', 'estado' => 'Ocupado',    'sala' => 'Sala 101', 'tipo_uso' => 'Trabalho de Grupo'],
                        ['inicio' => '12:00', 'fim' => '13:00', 'estado' => 'Disponível', 'sala' => 'Auditório A', 'tipo_uso' => 'Reunião'],
                        ['inicio' => '13:00', 'fim' => '14:00', 'estado' => 'Disponível', 'sala' => 'Sala 103', 'tipo_uso' => 'Estudo Individual'],
                        ['inicio' => '14:00', 'fim' => '15:00', 'estado' => 'Disponível', 'sala' => 'Sala 202', 'tipo_uso' => 'Trabalho de Grupo'],
                        ['inicio' => '15:00', 'fim' => '16:00', 'estado' => 'Ocupado',    'sala' => 'Sala 102', 'tipo_uso' => 'Estudo Individual'],
                        ['inicio' => '16:00', 'fim' => '17:00', 'estado' => 'Disponível', 'sala' => 'Sala 203', 'tipo_uso' => 'Trabalho de Grupo'],
                        ['inicio' => '17:00', 'fim' => '18:00', 'estado' => 'Disponível', 'sala' => 'Sala 104', 'tipo_uso' => 'Estudo Individual'],
                    ];
                @endphp

                @foreach($blocosHorarios as $item)
                    @php
                        $inicio = is_array($item) ? $item['inicio'] : substr($item->hora_inicio, 0, 5);
                        $fim = is_array($item) ? $item['fim'] : substr($item->hora_fim, 0, 5);
                        $estado = is_array($item) ? $item['estado'] : ($item->estado ?? 'Disponível');
                        $nomeSala = is_array($item) ? $item['sala'] : ($item->sala->nome_numero ?? $item->sala_nome ?? '—');
                        $tipoUso = is_array($item) ? $item['tipo_uso'] : ($item->tipo_de_uso ?? 'Estudo Individual');

                        $isOcupado = strtolower($estado) === 'ocupado' || strtolower($estado) === 'lotação esgotada';
                    @endphp
                    <tr>
                        <td class="fw-bold text-white fs-6">
                            <i class="bi bi-clock me-2 text-white-50"></i>
                            {{ $inicio }} — {{ $fim }}
                        </td>
                        <td>
                            @if(!$isOcupado)
                                <span class="badge-status badge-livre">Disponível</span>
                            @else
                                <span class="badge-status badge-ocupado">Lotação Esgotada</span>
                            @endif
                        </td>
                        <td class="fw-bold text-white">
                            <i class="bi bi-door-open me-1 text-white-50"></i>
                            {{ $nomeSala }}
                        </td>
                        <td class="text-white-50">
                            {{ $tipoUso }}
                        </td>
                        <td class="text-end">
                            @if(!$isOcupado)
                                <a href="{{ route('reservas.create', ['data' => $dataString, 'horario' => $inicio, 'sala' => $nomeSala]) }}" class="btn-liquid-sm">
                                    Reservar <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            @else
                                <span class="text-white-50 small pe-2">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</main>

<!-- ================= 3. RODAPÉ ================= -->
<footer class="p-4 text-center text-white-50 text-uppercase position-relative z-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
    © {{ date('Y') }} Universidade Fernando Pessoa · Gestão de Espaços
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

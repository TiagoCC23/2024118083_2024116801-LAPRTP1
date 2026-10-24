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
        .font-display {
            font-family: 'Syne', sans-serif;
            letter-spacing: -0.04em;
        }
        .font-body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Fundo Geral da Página (Fundo escuro com a imagem comentada) */
        body {
            /*background: #09090b;*/
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)),
                        url("{{ asset('images/fundo4.webp') }}") center / cover no-repeat fixed;
            color: #ffffff;
        }

        /* Efeito Liquid Glass (Glassmorphism) */
        .liquid-glass-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1.5rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Form Inputs e Selects em estilo Minimalista / Glass */
        .glass-input, .glass-select {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease;
        }
        .glass-input:focus, .glass-select:focus {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: #ffffff !important;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
        .glass-select option {
            background-color: #121214 !important;
            color: #ffffff !important;
        }

        /* Botão de Submissão Liquid */
        .btn-liquid {
            background-color: #ffffff;
            color: #000000;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.85rem;
            padding: 0.85rem 2rem;
            border-radius: 50rem;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.15);
        }
        .btn-liquid:hover {
            background-color: #e2e8f0;
            color: #000000;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(255, 255, 255, 0.25);
        }

        /* Card da Data Selecionada (Esquerda) */
        .date-display-box {
            text-align: center;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .date-number {
            font-size: 3.5rem;
            line-height: 1;
            font-weight: 800;
            font-family: 'Syne', sans-serif;
            color: #ffffff;
        }

        /* Texto digitado pelo utilizador */
        .glass-input {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important; /* ← Mude aqui a cor do texto digitado (ex: #ffffff, #38bdf8, etc.) */
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease;
        }

        /* Cor do texto de exemplo (Placeholder) */
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.4) !important;
        }

        /* Cor do texto quando o campo está em foco (selecionado) */
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: #ffffff !important;
            color: #ffffff !important; /* ← Cor do texto em foco */
        }
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

                <!-- Formulário de Reserva Unificado -->
                <form action="{{ route('reservas.store') }}" method="POST">
                    @csrf

                    <!-- Campo Oculto que envia a data para o banco de dados -->
                    <input type="hidden" name="data" value="{{ $dataString }}">

                    <!-- Edifício -->
                    <div class="mb-3">
                        <label for="edificio_id" class="form-label text-uppercase text-white-50 small" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                            Edifício *
                        </label>
                        <select id="edificio_id" name="edificio_id" required class="form-select glass-select">
                            <option value="" disabled selected>Selecione o Edifício</option>
                            @foreach($edificios ?? [] as $edificio)
                                <option value="{{ $edificio->id }}">{{ $edificio->nome }} ({{ $edificio->sigla }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sala -->
                    <div class="mb-3">
                        <label for="sala_id" class="form-label text-uppercase text-white-50 small" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                            Sala Disponível *
                        </label>
                        <input type="text" id="tipo_de_uso" name="tipo_de_uso" required
                               placeholder="Ex: Sala 204" class="form-control glass-input">
                    </div>

                    <!-- Tipo de Uso e Quantidade de Pessoas -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="tipo_de_uso" class="form-label text-uppercase text-white-50 small" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                                Tipo de Uso *
                            </label>
                            <input type="text" id="tipo_de_uso" name="tipo_de_uso" required
                                   placeholder="Ex: Estudo Individual, Reunião, etc." class="form-control glass-input">
                        </div>
                        <div class="col-md-6">
                            <label for="quantidade_pessoas" class="form-label text-uppercase text-white-50 small" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                                Nº Pessoas *
                            </label>
                            <input type="number" id="quantidade_pessoas" name="quantidade_pessoas" min="1" max="50" value="1" required class="form-control glass-input">
                        </div>
                    </div>

                    <!-- Bloco de Horários -->
                    <div class="mb-4">
                        <label class="form-label text-uppercase text-white-50 small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                            Horários Pretendidos *
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($horarios ?? [] as $horario)
                                @php
                                    // Corta os segundos (ex: "08:00:00" passa a "08:00")
                                    $inicio = substr($horario->hora_inicio, 0, 5);
                                    $fim = substr($horario->hora_fim, 0, 5);
                                @endphp
                                <input type="checkbox" class="btn-check" name="horarios[]" id="h-{{ $horario->id }}" value="{{ $horario->id }}" autocomplete="off">
                                <label class="btn btn-outline-light rounded-pill px-3 py-2" style="font-size: 0.8rem;" for="h-{{ $horario->id }}">
                                    {{ $inicio }} - {{ $fim }}
                                </label>
                            @empty
                                <span class="text-white-50 small">Carregando horários disponíveis...</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Botão de Confirmação -->
                    <div class="pt-2 text-end">
                        <button type="submit" class="btn btn-liquid w-100 w-md-auto">
                            <i class="bi bi-check2-circle me-1"></i> Confirmar Reserva
                        </button>
                    </div>
                </form>
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

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UFP - Painel Principal</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Plus+Jakarta+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Flatpickr (Calendário Dinâmico) CSS + Tema Dark -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

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

        /* Card Principal com Imagem da Faculdade */
        .glass-card-campus {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.9)),
            url("{{ asset('images/fundo3.jpg') }}") center / cover no-repeat;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        /* Botão Minimalista Destaque */
        .btn-reserve {
            background-color: #ffffff;
            color: #000000;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.85rem;
            padding: 1rem 2rem;
            border-radius: 50rem;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.1);
            display: inline-block;
            text-decoration: none;
        }
        .btn-reserve:hover {
            background-color: #e2e8f0;
            color: #000000;
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(255, 255, 255, 0.2);
        }

        /* Botões Secundários Glass (Minhas Reservas & Horários) */
        .btn-glass-secondary {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-glass-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-1px);
        }

        /* Customização do Flatpickr */
        .calendar-box {
            background: rgba(0, 0, 0, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            border-radius: 1rem !important;
            padding: 1rem !important;
        }

        .flatpickr-calendar,
        .flatpickr-months,
        .flatpickr-month,
        .flatpickr-current-month,
        .flatpickr-weekdays,
        span.flatpickr-weekday {
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
        }

        .flatpickr-months .flatpickr-month {
            color: #ffffff !important;
            fill: #ffffff !important;
            padding-bottom: 0.5rem !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            color: #ffffff !important;
            font-weight: 700 !important;
            background: transparent !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months option {
            background-color: #18181b !important;
            color: #ffffff !important;
        }

        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            fill: rgba(255, 255, 255, 0.8) !important;
        }
        .flatpickr-months .flatpickr-prev-month:hover svg,
        .flatpickr-months .flatpickr-next-month:hover svg {
            fill: #ffffff !important;
        }

        span.flatpickr-weekday {
            color: rgba(255, 255, 255, 0.5) !important;
            font-size: 0.7rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        .flatpickr-day {
            color: rgba(255, 255, 255, 0.85) !important;
            border-radius: 0.5rem !important;
            border: none !important;
        }
        .flatpickr-day:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }
        .flatpickr-day.selected {
            background: #ffffff !important;
            color: #000000 !important;
            font-weight: bold !important;
        }
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: rgba(255, 255, 255, 0.2) !important;
        }
    </style>
</head>
<body class="font-body min-vh-100 d-flex flex-column justify-content-between">

<!-- NAVEGAÇÃO -->
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
<main class="container my-auto py-4 position-relative z-3" style="max-width: 960px;">

    <div class="glass-card-campus p-4 p-md-5">
        <div class="row g-4 align-items-center">

            <!-- LADO ESQUERDO: CALENDÁRIO DINÂMICO & BOTÕES DE ATALHO -->
            <div class="col-lg-6">
                <div class="calendar-box">
                    <div class="text-uppercase fw-bold text-white small mb-2 d-flex align-items-center justify-content-between" style="letter-spacing: 0.15em;">
                        <span><i class="bi bi-calendar3 me-2 text-white-50"></i> Selecione a Data</span>
                        <span id="data-selecionada-badge" class="badge bg-white text-dark fw-bold">Hoje</span>
                    </div>

                    <!-- Div onde o Flatpickr é renderizado -->
                    <div id="calendar-inline"></div>

                    <!-- BOTÕES ADICIONADOS ABAIXO DO CALENDÁRIO -->
                    <div class="row g-2 mt-3 pt-2 border-top border-white-10">
                        <div class="col-6">
                            <a href="{{ route('reservas.index') }}" class="btn-glass-secondary w-100">
                                <i class="bi bi-journal-text me-1.5"></i> Ver Reservas
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('horarios.index') }}" class="btn-glass-secondary w-100">
                                <i class="bi bi-clock-history me-1.5"></i> Ver Horários
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LADO DIREITO: FORMULÁRIO COM O BOTÃO PRINCIPAL -->
            <div class="col-lg-6 text-center text-lg-start ps-lg-4">

                <span class="badge bg-white bg-opacity-10 text-white border border-white-20 rounded-pill px-3 py-1 text-uppercase mb-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
                    Universidade Fernando Pessoa
                </span>

                <h2 class="font-display display-5 fw-bold text-uppercase text-white lh-1 mb-3">
                    Espaços de Estudo
                </h2>

                <p class="text-white-50 small mb-4" style="line-height: 1.6;">
                    Selecione uma data no calendário ao lado e clique no botão abaixo para prosseguir com a reserva das salas.
                </p>

                <!-- Form que envia a data escolhida para a rota de criação da reserva -->
                <form action="{{ route('reservas.create') }}" method="GET">
                    <!-- Campo oculto que vai guardar a data no formato YYYY-MM-DD -->
                    <input type="hidden" name="data" id="input-data-selecionada">

                    <button type="submit" class="btn btn-reserve w-100">
                        <i class="bi bi-plus-lg me-2"></i> Fazer minha reserva
                    </button>
                </form>

            </div>

        </div>
    </div>

</main>

<!-- RODAPÉ -->
<footer class="p-4 text-center text-white-50 text-uppercase position-relative z-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
    © {{ date('Y') }} Universidade Fernando Pessoa · Gestão de Espaços
</footer>

<!-- JS do Bootstrap & Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- Tradução em Português -->
<script src="https://npmcdn.com/flatpickr/dist/l10n/pt.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputData = document.getElementById('input-data-selecionada');
        const badgeData = document.getElementById('data-selecionada-badge');

        // Inicializa o Calendário Dinâmico
        flatpickr("#calendar-inline", {
            inline: true,             // Exibe o calendário sempre aberto na tela
            locale: "pt",             // Idioma em Português
            minDate: "today",         // Não permite reservar datas passadas
            dateFormat: "Y-m-d",      // Formato para enviar ao Laravel
            defaultDate: "today",     // Seleciona o dia atual por padrão
            onChange: function(selectedDates, dateStr) {
                // Atualiza o input escondido
                inputData.value = dateStr;

                // Formata a data para a badge
                const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
                badgeData.innerText = selectedDates[0].toLocaleDateString('pt-PT', options);
            }
        });

        // Define a data inicial no input escondido
        const hoje = new Date().toISOString().split('T')[0];
        inputData.value = hoje;
    });
</script>

</body>
</html>

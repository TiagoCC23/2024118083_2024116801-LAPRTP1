<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UFP - Salas de Estudo</title>

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
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)),
            url("{{ asset('images/fundo1.jpg') }}") center / cover no-repeat fixed;
            color: #ffffff;
        }

        /* Inputs minimalistas em Bootstrap */
        .minimal-input {
            background: transparent !important;
            border: none !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.5) !important;
            color: #ffffff !important;
            border-radius: 0 !important;
            padding: 0.5rem 0 !important;
            box-shadow: none !important;
        }
        .minimal-input:focus {
            border-bottom: 2px solid #ffffff !important;
            color: #ffffff !important;
        }
        .minimal-input::placeholder {
            color: rgba(255, 255, 255, 0.4) !important;
        }

        /* Botão arredondado minimalista */
        .btn-minimal {
            background-color: #ffffff;
            color: #000000;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            padding: 0.75rem 2rem;
            border-radius: 50rem;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-minimal:hover {
            background-color: #e2e8f0;
            color: #000000;
            transform: translateY(-1px);
        }

        /* Abas ativas */
        .tab-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding-bottom: 0.25rem;
            transition: all 0.3s ease;
        }
        .tab-btn.active {
            color: #ffffff;
            font-weight: 700;
            border-bottom: 2px solid #ffffff;
        }
    </style>
</head>
<body class="font-body min-vh-100 d-flex flex-column justify-content-between">

<!-- Cabeçalho -->
<header class="p-4 p-md-5 d-flex justify-content-between align-items-center position-relative z-3">
    <a href="{{ url('/') }}" class="text-white text-decoration-none text-uppercase fw-semibold border-bottom border-white pb-1" style="font-size: 0.85rem; letter-spacing: 0.15em;">
        Início
    </a>

    <!-- Botões para alternar Login / Registar -->
    <div class="d-flex align-items-center gap-3">
        <button type="button" onclick="switchTab('login')" id="btn-tab-login" class="tab-btn active">
            Entrar
        </button>
        <span class="text-white-50">|</span>
        <button type="button" onclick="switchTab('register')" id="btn-tab-register" class="tab-btn">
            Registar
        </button>
    </div>
</header>

<!-- Conteúdo Central -->
<main class="container my-auto py-4 position-relative z-3" style="max-width: 580px;">
    <div class="text-center">

        <!-- TÍTULO PRINCIPAL -->
        <h1 class="font-display display-1 fw-bold text-uppercase text-white lh-1 mb-2 select-none">
            UFP<br>SALAS
        </h1>

        <!-- Subtítulo -->
        <p class="text-uppercase text-white-50 font-medium mb-5" style="letter-spacing: 0.3em; font-size: 0.8rem;">
            Reserva de Espaços de Estudo
        </p>

        <!-- Mensagens de Erro ou Sucesso -->
        @if ($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-20 border-danger border-opacity-50 text-white text-start p-3 rounded-3 mb-4 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success bg-success bg-opacity-20 border-success border-opacity-50 text-white p-3 rounded-3 mb-4 text-xs">
                {{ session('status') }}
            </div>
        @endif

        <!-- ================= FORMULÁRIO 1: LOGIN ================= -->
        <form id="form-login" action="{{ route('login') }}" method="POST" class="text-start">
            @csrf

            <!-- Campo Email -->
            <div class="mb-4">
                <label for="login-email" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    Email Institucional *
                </label>
                <input type="email" id="login-email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="aluno@ufp.edu.pt" class="form-control minimal-input">
            </div>

            <!-- Campo Senha -->
            <div class="mb-4">
                <label for="login-password" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    Palavra-passe *
                </label>
                <input type="password" id="login-password" name="password" required
                       placeholder="••••••••" class="form-control minimal-input">
            </div>

            <!-- Checkbox e Botão -->
            <div class="pt-2 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input bg-transparent border-white-50">
                    <label for="remember" class="form-check-label text-white-50 small cursor-pointer select-none">
                        Manter sessão iniciada
                    </label>
                </div>

                <button type="submit" class="btn btn-minimal w-100 w-sm-auto">
                    Entrar
                </button>
            </div>
        </form>

        <!-- ================= FORMULÁRIO 2: REGISTO ================= -->
        <form id="form-register" action="{{ route('register') }}" method="POST" class="text-start d-none">
            @csrf

            <!-- Campo Nome -->
            <div class="mb-4">
                <label for="reg-nome" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    Nome Completo *
                </label>
                <input type="text" id="reg-nome" name="nome" value="{{ old('nome') }}" required
                       placeholder="Seu Nome" class="form-control minimal-input">
            </div>

            <!-- Campo Email -->
            <div class="mb-4">
                <label for="reg-email" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    Email *
                </label>
                <input type="email" id="reg-email" name="email" value="{{ old('email') }}" required
                       placeholder="seu.email@ufp.edu.pt" class="form-control minimal-input">
            </div>

            <!-- Campo Palavra-passe -->
            <div class="mb-4">
                <label for="reg-password" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    Criar Palavra-passe *
                </label>
                <input type="password" id="reg-password" name="password" required
                       placeholder="Mínimo 6 caracteres" class="form-control minimal-input">
            </div>

            <!-- Confirmação da Palavra-passe -->
            <div class="mb-4">
                <label for="reg-password-confirm" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    Confirmar Palavra-passe *
                </label>
                <input type="password" id="reg-password-confirm" name="password_confirmation" required
                       placeholder="Repita a palavra-passe" class="form-control minimal-input">
            </div>

            <!-- Termos e Botão -->
            <div class="pt-2 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div class="form-check">
                    <input type="checkbox" id="terms" required class="form-check-input bg-transparent border-white-50">
                    <label for="terms" class="form-check-label text-white-50 small cursor-pointer select-none">
                        Aceito os termos de reserva da UFP *
                    </label>
                </div>

                <button type="submit" class="btn btn-minimal w-100 w-sm-auto">
                    Criar Conta
                </button>
            </div>
        </form>

    </div>
</main>

<footer class="p-4 text-center text-white-50 text-uppercase position-relative z-3" style="font-size: 0.65rem; letter-spacing: 0.15em;">
    © {{ date('Y') }} Universidade Fernando Pessoa · Gestão de Espaços
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para alternar entre Login e Sign Up -->
<script>
    function switchTab(mode) {
        const formLogin = document.getElementById('form-login');
        const formRegister = document.getElementById('form-register');
        const btnLogin = document.getElementById('btn-tab-login');
        const btnRegister = document.getElementById('btn-tab-register');

        if (mode === 'login') {
            formLogin.classList.remove('d-none');
            formRegister.classList.add('d-none');

            btnLogin.classList.add('active');
            btnRegister.classList.remove('active');
        } else {
            formLogin.classList.add('d-none');
            formRegister.classList.remove('d-none');

            btnRegister.classList.add('active');
            btnLogin.classList.remove('active');
        }
    }
</script>
</body>
</html>

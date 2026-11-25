@php
    $isRegisto = old('_form') === 'registo' || ($tab ?? 'login') === 'registo';
    $fundo = 'images/fundo1.jpg';
@endphp

@extends('layouts.app')

@section('title', 'UFP - Gestão de Espaços')

@push('styles')
    <style>
        .btn-login-submit {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.08em !important;
            font-size: 0.8rem !important;
            padding: 0.75rem 2.8rem !important;
            border-radius: 50rem !important;
            border: none !important;
            display: inline-block !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
        }

        .btn-login-submit:hover {
            background-color: #e2e8f0 !important;
            color: #000000 !important;
            transform: translateY(-1px);
        }

        .minimal-input {
            background: transparent !important;
            border: none !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.5) !important;
            color: #ffffff !important;
            border-radius: 0 !important;
            padding: 0.6rem 0 !important;
            box-shadow: none !important;
        }

        .minimal-input:focus {
            background: transparent !important;
            border-bottom: 2px solid #ffffff !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .minimal-input::placeholder {
            color: rgba(255, 255, 255, 0.35) !important;
        }

        .tab-btn {
            background: none !important;
            border: none !important;
            color: rgba(255, 255, 255, 0.5) !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.12em !important;
            padding-bottom: 0.25rem !important;
            cursor: pointer !important;
            transition: all 0.2s ease;
        }

        .tab-btn.active {
            color: #ffffff !important;
            font-weight: 700 !important;
            border-bottom: 2px solid #ffffff !important;
        }
    </style>
@endpush

{{-- Cabeçalho com as abas --}}
@section('header')
    <header class="p-4 p-md-5 d-flex justify-content-end align-items-center position-relative z-3">
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="btn-tab-login" class="tab-btn {{ !$isRegisto ? 'active' : '' }}">
                ENTRAR
            </button>
            @if (session()->has('google_registo'))
                <span class="text-white-50">|</span>
                <button type="button" id="btn-tab-register" class="tab-btn {{ $isRegisto ? 'active' : '' }}">
                    CONCLUIR REGISTO
                </button>
            @endif
        </div>
    </header>
@endsection

{{-- Conteúdo --}}
@section('conteudo')
    <div class="mx-auto text-center" style="max-width: 580px;">

        <!-- Título Principal -->
        <h1 class="font-display display-1 fw-bold text-uppercase text-white lh-1 mb-2 user-select-none" style="letter-spacing: -0.04em;">
            UFP<br>SALAS
        </h1>

        <!-- Subtítulo -->
        <p class="text-uppercase text-white-50 fw-semibold mb-5" style="letter-spacing: 0.3em; font-size: 0.8rem;">
            Reserva de Espaços de Estudo
        </p>

        <!-- Erros de Validação -->
        @if ($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-20 border-danger text-white text-start p-3 rounded-3 mb-4 small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulário 1: Login -->
        <form id="form-login" action="{{ route('login') }}" method="POST" class="text-start {{ $isRegisto ? 'd-none' : '' }}">
            @csrf

            <div class="mb-4 text-center">
                <a href="{{ route('auth.google') }}"
                   class="btn btn-outline-light w-100 rounded-pill py-2 fw-semibold text-uppercase d-flex align-items-center justify-content-center gap-2"
                   style="letter-spacing: 0.08em; font-size: 0.8rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M21.35 11.1H12v2.98h5.35c-.23 1.48-1.72 4.34-5.35 4.34-3.22 0-5.85-2.67-5.85-5.95s2.63-5.95 5.85-5.95c1.83 0 3.06.78 3.76 1.45l2.56-2.47C16.68 3.96 14.55 3 12 3 7.03 3 3 7.03 3 12s4.03 9 9 9c5.19 0 8.64-3.65 8.64-8.79 0-.59-.06-1.04-.14-1.11z"/>
                    </svg>
                    Continuar com o Google
                </a>
            </div>

            <div class="d-flex align-items-center gap-3 my-4 text-white-50 small">
                <hr class="flex-grow-1 border-white-50">
                <span class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.15em;">ou com email</span>
                <hr class="flex-grow-1 border-white-50">
            </div>

            <div class="mb-4">
                <label for="login-email" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    EMAIL *
                </label>
                <input type="email" id="login-email" name="email" value="{{ old('email') }}" required
                       placeholder="exemplo@ufp.edu.pt" class="form-control minimal-input">
            </div>

            <div class="mb-5">
                <label for="login-password" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    PALAVRA-PASSE *
                </label>
                <input type="password" id="login-password" name="password" required
                       placeholder="••••••••" class="form-control minimal-input">
            </div>

            <div class="pt-2 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div class="form-check d-flex align-items-center gap-2 m-0">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input bg-transparent border-white-50 mt-0">
                    <label for="remember" class="form-check-label text-white-50 small cursor-pointer user-select-none">
                        Manter sessão iniciada
                    </label>
                </div>

                <button type="submit" class="btn-login-submit">
                    ENTRAR
                </button>
            </div>
        </form>

        <!-- Formulário 2: Registo -->
        <form id="form-register" action="{{ route('auth.signin') }}" method="POST" class="text-start {{ !$isRegisto ? 'd-none' : '' }}">
            @csrf
            <input type="hidden" name="_form" value="registo">

            <div class="mb-4">
                <label for="reg-nome" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    NOME COMPLETO *
                </label>
                <input type="text" id="reg-nome" name="nome"
                       value="{{ old('nome', session('google_registo.nome')) }}" required
                       placeholder="Nome" class="form-control minimal-input">
            </div>

            <div class="mb-4">
                <label for="reg-email" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    EMAIL INSTITUCIONAL (GOOGLE)
                </label>
                <input type="email" id="reg-email" value="{{ session('google_registo.email') }}"
                       readonly class="form-control minimal-input opacity-75">
            </div>

            <div class="mb-4">
                <label for="reg-password" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    PALAVRA-PASSE (MÍN. 8 CARACTERES) *
                </label>
                <input type="password" id="reg-password" name="password" required
                       placeholder="••••••••" class="form-control minimal-input">
            </div>

            <div class="mb-5">
                <label for="reg-password-confirm" class="form-label text-uppercase text-white-50 small mb-1" style="letter-spacing: 0.1em; font-size: 0.75rem;">
                    CONFIRMAR PALAVRA-PASSE *
                </label>
                <input type="password" id="reg-password-confirm" name="password_confirmation" required
                       placeholder="••••••••" class="form-control minimal-input">
            </div>

            <div class="pt-2 d-flex justify-content-end">
                <button type="submit" class="btn-login-submit">
                    CRIAR CONTA
                </button>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('#btn-tab-login, #btn-tab-register');
            if (!btn) return;

            e.preventDefault();

            const formLogin = document.getElementById('form-login');
            const formRegister = document.getElementById('form-register');
            const btnLogin = document.getElementById('btn-tab-login');
            const btnRegister = document.getElementById('btn-tab-register');

            if (!formLogin || !formRegister) return;

            if (btn.id === 'btn-tab-login') {
                formLogin.classList.remove('d-none');
                formRegister.classList.add('d-none');
                btnLogin?.classList.add('active');
                btnRegister?.classList.remove('active');
                window.history.replaceState(null, '', '/login');
            } else if (btn.id === 'btn-tab-register') {
                formLogin.classList.add('d-none');
                formRegister.classList.remove('d-none');
                btnRegister?.classList.add('active');
                btnLogin?.classList.remove('active');
                window.history.replaceState(null, '', '/registo');
            }
        });
    </script>
@endpush

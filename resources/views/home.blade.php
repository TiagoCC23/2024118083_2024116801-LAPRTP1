@extends('layouts.app')

@section('title', 'UFP - Home')
@section('fundo', asset('images/fundo3.jpg'))

@section('conteudo')
    <div class="glass-card-campus p-4 p-md-5 mx-auto" style="max-width: 960px;">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="calendar-box">
                    <div class="text-uppercase fw-bold text-white small mb-2 d-flex align-items-center justify-content-between" style="letter-spacing: 0.15em;">
                        <span><i class="bi bi-calendar3 me-2 text-white-50"></i> Selecione a Data</span>
                        <span id="data-selecionada-badge" class="badge bg-white text-dark fw-bold">Hoje</span>
                    </div>

                    <div id="calendar-inline"></div>

                    <div class="row g-2 mt-3 pt-2 border-top border-white border-opacity-10">
                        <div class="col-6">
                            <a href="{{ route('reservas.index') }}" class="btn-glass-secondary w-100">
                                <i class="bi bi-journal-text me-1"></i> Ver Reservas
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('horarios.index') }}" class="btn-glass-secondary w-100">
                                <i class="bi bi-clock-history me-1"></i> Ver Horários
                            </a>
                        </div>
                    </div>
                </div>
            </div>

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

                <form action="{{ route('reservas.create') }}" method="GET">
                    <input type="hidden" name="data" id="input-data-selecionada" value="{{ date('Y-m-d') }}">
                    <button type="submit" class="btn btn-liquid w-100">
                        <i class="bi bi-plus-lg me-2"></i> Fazer minha reserva
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const calendarInline = document.getElementById('calendar-inline');
            if (calendarInline && typeof window.flatpickr !== 'undefined') {
                const inputData = document.getElementById('input-data-selecionada');
                const badgeData = document.getElementById('data-selecionada-badge');

                const dataInicial = new Date();
                if (dataInicial.getDay() === 0) { // se for Dom, ele pula para Seg
                    dataInicial.setDate(dataInicial.getDate() + 1);
                }

                window.flatpickr(calendarInline, {
                    inline: true,
                    locale: "pt",
                    monthSelectorType: "static",
                    minDate: "today",
                    dateFormat: "Y-m-d",
                    defaultDate: "today",
                    onChange: function (selectedDates, dateStr) {
                        if (inputData) inputData.value = dateStr;
                        if (badgeData && selectedDates[0]) {
                            const hoje = new Date().toDateString();
                            badgeData.innerText = selectedDates[0].toDateString() === hoje
                                ? 'Hoje'
                                : selectedDates[0].toLocaleDateString('pt-PT');
                        }
                    }
                });
            }
        });
    </script>
@endpush

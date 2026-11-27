<h2>Olá, {{ $reserva->utilizador->nome }}!</h2>

<p>A tua reserva no sistema <strong>UFP Salas</strong> foi <strong>aprovada</strong>.</p>

<ul>
    <li><strong>Sala:</strong> {{ $reserva->sala->nome_numero ?? $reserva->sala->nome }}</li>
    <li><strong>Data:</strong> {{ \Carbon\Carbon::parse($reserva->data)->format('d/m/Y') }}</li>
    @if ($reserva->horarios->isNotEmpty())
        <li>
            <strong>Horário(s):</strong>
            {{ $reserva->horarios->pluck('intervalo')->implode(', ') }}
        </li>
    @endif
</ul>

<h2>Olá, {{ $reserva->utilizador->nome }}!</h2>

<p>O teu pedido de reserva foi registado com sucesso no sistema <strong>UFP Salas</strong>.</p>

<ul>
    <li><strong>Sala:</strong> {{ $reserva->sala->nome_numero ?? $reserva->sala->nome }}</li>
    <li><strong>Data:</strong> {{ \Carbon\Carbon::parse($reserva->data)->format('d/m/Y') }}</li>
    @if ($reserva->horarios->isNotEmpty())
        <li>
            <strong>Horário(s):</strong>
            {{ $reserva->horarios->pluck('intervalo')->implode(', ') }}
        </li>
    @endif
    <li><strong>Estado atual:</strong> {{ $reserva->estado->value ?? $reserva->estado }}</li>
</ul>

<p>Receberás uma atualização assim que o estado da reserva for revisto.</p>

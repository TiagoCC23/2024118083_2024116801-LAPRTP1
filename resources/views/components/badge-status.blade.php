@props(['estado'])

@php
    $estadoValor = strtolower(is_object($estado) ? ($estado->value ?? '') : (string) $estado);
    $classe = match($estadoValor) {
        'confirmada', 'disponivel', 'livre' => 'badge-confirmada',
        'cancelada', 'rejeitada', 'ocupado' => 'badge-cancelada',
        default => 'badge-pendente',
    };
@endphp

<span class="badge-status {{ $classe }}">
    {{ ucfirst($estadoValor) }}
</span>

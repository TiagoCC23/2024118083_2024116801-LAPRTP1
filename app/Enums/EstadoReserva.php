<?php

namespace App\Enums;

enum EstadoReserva: string
{
    case Pendente = 'pendente';
    case Confirmada = 'confirmada';
    case Rejeitada = 'rejeitada';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match($this) {
            self::Pendente => 'Pendente',
            self::Confirmada => 'Confirmada',
            self::Rejeitada => 'Rejeitada',
            self::Cancelada => 'Cancelada',
        };
    }
}

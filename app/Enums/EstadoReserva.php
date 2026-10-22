<?php

namespace App\Enums;

enum EstadoReserva: string
{
    case Pendente = 'Pendente';
    case Confirmada = 'Confirmada';
    case Rejeitada = 'Rejeitada';
    case Cancelada = 'Cancelada';

    public function label(): string
    {
        return $this->value;
    }
}

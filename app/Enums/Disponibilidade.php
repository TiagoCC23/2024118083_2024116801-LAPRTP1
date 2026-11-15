<?php

namespace App\Enums;

enum Disponibilidade: string
{
    case Disponivel = 'disponivel';
    case Pendente = 'pendente';
    case Ocupado = 'ocupado';

    public function label(): string
    {
        return match($this) {
            self::Disponivel => 'Disponivel',
            self::Pendente => 'Pendente',
            self::Ocupado => 'Ocupado',
        };
    }
}

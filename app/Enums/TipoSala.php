<?php

namespace App\Enums;

enum TipoSala: string
{
    case SalaDeEstudo = 'Sala de estudo';
    case Laboratorio = 'Laboratório';
    case SalaDeAula = 'Sala de aula';

    public function label(): string
    {
        return $this->value;
    }
}

<?php

namespace App\Enums;

enum TipoDeUso: string
{
    case SalaDeEstudo = 'Exame';
    case Laboratorio = 'Trabalho';
    case Leitura = 'Leitura';
    case Outro = 'Outro';

    public function label(): string
    {
        return $this->value;
    }
}

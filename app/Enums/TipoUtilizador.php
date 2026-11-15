<?php

namespace App\Enums;

enum TipoUtilizador: string
{
    case Estudante = 'estudante';
    case Admin = 'admin';

    public function label(): string
    {
        return match($this) {
            self::Estudante => 'Estudante',
            self::Admin => 'Administrador',
        };
    }
}
